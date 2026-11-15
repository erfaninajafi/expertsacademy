#!/usr/bin/env python3
"""Run independent read-only component shards and verify exhaustive merged coverage."""
import argparse
import json
import os
import subprocess
import time
from pathlib import Path
from xml.etree import ElementTree

ROOT=Path(__file__).resolve().parents[2]
parser=argparse.ArgumentParser()
parser.add_argument('--mode',choices=['baseline','comparison'],required=True)
parser.add_argument('--visual-root',type=Path,required=True)
parser.add_argument('--workers',type=int,default=4,choices=[1,2,4])
args=parser.parse_args()
catalog=json.loads((ROOT/'include_site/studio/catalog/components.v1.json').read_text())
types=[d['type'] for d in catalog['components']]
def fingerprint():
    return subprocess.check_output(['rtk','proxy','/opt/lampp/bin/php','-r','require "include_site/studio/bootstrap.php";echo ExpertsAcademy\\Studio\\QualityReport::fingerprint();'],cwd=ROOT,text=True).strip()
initial=fingerprint();jobs=[]
for i in range(args.workers):
    selected=types[i::args.workers];env=os.environ.copy();env.pop('EA_STUDIO_SCENARIOS',None)
    report=f'matrix-{args.mode}-{i}.json';xml=f'/tmp/ea-phase2-{args.mode}-{i}.xml';log=Path(f'/tmp/ea-phase2-{args.mode}-{i}.log')
    env.update({'EA_STUDIO_BROWSER_PATH':'/opt/google/chrome/chrome','EA_STUDIO_VISUAL':'1','EA_STUDIO_TYPES':','.join(selected),'EA_STUDIO_MATRIX_REPORT':report,'EA_STUDIO_TEST_OUTPUT':f'/tmp/ea-phase2-{args.mode}-{i}-output','PLAYWRIGHT_JUNIT_OUTPUT_FILE':xml})
    command=['rtk','proxy','npm','run','test:e2e','--','e2e/component-matrix.spec.ts','--reporter=list,junit']
    if args.mode=='baseline':command.append('--update-snapshots')
    output=log.open('w');process=subprocess.Popen(command,cwd=ROOT/'site_admin/studio',env=env,stdout=output,stderr=subprocess.STDOUT)
    jobs.append((process,output,log,report,xml));print(f'Started {args.mode} shard {i+1}: {len(selected)} core types',flush=True)
while any(p.poll() is None for p,*_ in jobs):
    time.sleep(15)
    progress=[]
    for p,_,log,_,_ in jobs:
        lines=[l for l in log.read_text().splitlines() if 'Completed category' in l or 'Matrix complete:' in l]
        progress.append(lines[-1] if lines else ('running' if p.poll() is None else f'exit {p.returncode}'))
    print(json.dumps({'progress':progress}),flush=True)
for _,output,*_ in jobs:output.close()
failed=[str(log) for p,_,log,_,_ in jobs if p.returncode]
if failed:raise SystemExit('Failed matrix shard logs: '+', '.join(failed))
reports=[json.loads((args.visual_root/report).read_text()) for _,_,_,report,_ in jobs]
assert initial==fingerprint(),'Source changed during matrix execution'
scenarios=reports[0]['scenarios'];assert len(scenarios)==6 and all(r['scenarios']==scenarios for r in reports)
results=[entry for report in reports for entry in report['results']]
observed=[(r['type'],r['version'],r['variant'],r['scenario']) for r in results]
expected={(d['type'],version,v['id'],s['id']) for d in catalog['components'] for version in ([1,2] if d['type']=='hero-editorial' else [1]) for v in d['variants'] for s in scenarios}
assert len(observed)==len(set(observed))==len(expected)==1464 and set(observed)==expected,'Missing or duplicate render coverage'
merged={'generatedAt':max(r['generatedAt'] for r in reports),'catalogFingerprint':initial,'executionShards':args.workers,'types':sum(r['types'] for r in reports),'variants':sum(r['variants'] for r in reports),'versionedDefinitions':sum(r['versionedDefinitions'] for r in reports),'versionedVariants':sum(r['versionedVariants'] for r in reports),'responsiveRenders':len(results),'axeAudits':sum(r['axeAudits'] for r in reports),'visualSnapshots':sum(r['visualSnapshots'] for r in reports),'scenarios':scenarios,'failures':[f for r in reports for f in r['failures']],'results':results}
assert merged['types']==60 and merged['variants']==240 and merged['versionedDefinitions']==61 and merged['axeAudits']==488 and not merged['failures']
(args.visual_root/f'component-matrix-{args.mode}.json').write_text(json.dumps(merged,indent=2)+'\n')
(args.visual_root/'component-matrix.json').write_text(json.dumps(merged,indent=2)+'\n')
xmlroot=ElementTree.Element('testsuites')
for i,(_,_,_,_,xml) in enumerate(jobs):
    root=ElementTree.parse(xml).getroot()
    for suite in list(root) if root.tag=='testsuites' else [root]:
        assert int(suite.get('failures','0'))==int(suite.get('errors','0'))==int(suite.get('skipped','0'))==0
        suite.set('name',suite.get('name','matrix')+f' / shard {i+1}');xmlroot.append(suite)
ElementTree.ElementTree(xmlroot).write(ROOT/'DUCS'/f'PHASE2_VISUAL_{args.mode.upper()}.xml',encoding='unicode',xml_declaration=True)
print(json.dumps({k:merged[k] for k in ['types','variants','versionedDefinitions','responsiveRenders','axeAudits','visualSnapshots','failures']}),flush=True)
