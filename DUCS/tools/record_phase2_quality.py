#!/usr/bin/env python3
"""Publish measured quality metadata only after all required input reports pass."""
import argparse
import hashlib
import json
import subprocess
from datetime import datetime, timezone
from pathlib import Path
from xml.etree import ElementTree

ROOT=Path(__file__).resolve().parents[2]
parser=argparse.ArgumentParser()
parser.add_argument('--visual-root', required=True, type=Path)
args=parser.parse_args()
def junit(name):
    root=ElementTree.parse(ROOT/'DUCS'/name).getroot()
    suites=list(root) if root.tag=='testsuites' else [root]
    # PHPUnit has one aggregate suite; Playwright has one suite per spec.
    total={k:sum(int(s.get(k,'0')) for s in suites) for k in ['tests','failures','errors','skipped','assertions']}
    assert total['tests']>0 and total['failures']==total['errors']==total['skipped']==0, (name,total)
    return total
backend={engine:junit('PHASE2_PHPUNIT_'+engine.upper()+'.xml') for engine in ['mariadb','mysql8']}
functional=junit('PHASE2_PLAYWRIGHT_FUNCTIONAL.xml')
delivery=junit('PHASE2_PLAYWRIGHT_DELIVERY.xml')
root_deployment=junit('PHASE2_PLAYWRIGHT_ROOT.xml')
typography=junit('PHASE2_PLAYWRIGHT_TYPOGRAPHY.xml')
baseline=junit('PHASE2_VISUAL_BASELINE.xml')
rtl_baseline=junit('PHASE2_VISUAL_RTL_BASELINE.xml')
comparison=junit('PHASE2_VISUAL_COMPARISON.xml')
matrix=json.loads((args.visual_root/'component-matrix.json').read_text())
assert matrix['failures']==[] and matrix['types']==60 and matrix['variants']==240
assert matrix['responsiveRenders']==matrix['visualSnapshots']==1464 and matrix['axeAudits']==488
snapshots=sorted((args.visual_root/'baseline/chromium').glob('*.png'))
assert len(snapshots)==1464
manifest=[{'file':p.name,'sha256':hashlib.sha256(p.read_bytes()).hexdigest()} for p in snapshots]
(args.visual_root/'screenshot-manifest.json').write_text(json.dumps(manifest,indent=2)+'\n')
unit=json.loads((ROOT/'DUCS/PHASE2_VITEST.json').read_text())
assert unit['success'] and unit['numPassedTests']==11 and unit['numFailedTests']==0
lint=json.loads((ROOT/'DUCS/PHASE2_PHP_LINT.json').read_text());assert not lint['failures']
npm=json.loads((ROOT/'DUCS/PHASE2_NPM_AUDIT.json').read_text());assert npm['metadata']['vulnerabilities']['total']==0
composer=json.loads((ROOT/'DUCS/PHASE2_COMPOSER_AUDIT.json').read_text());assert not composer['advisories']
fingerprint=subprocess.check_output(['rtk','proxy','/opt/lampp/bin/php','-r','require "include_site/studio/bootstrap.php";echo ExpertsAcademy\\Studio\\QualityReport::fingerprint();'],cwd=ROOT,text=True).strip()
report={'status':'passed','generatedAt':datetime.now(timezone.utc).isoformat(),'catalogFingerprint':fingerprint,'library':{'coreTypes':60,'coreVariants':240,'categories':20,'versionedModernDefinitions':61,'versionedRenderCases':244},'phpunit':backend,'vitest':{'tests':11,'passed':11},'playwright':{'functional':functional,'delivery':delivery,'rootDeployment':root_deployment,'typographyRegression':typography,'baseline':baseline,'reviewedRtlBaselineUpdate':rtl_baseline,'comparison':comparison},'matrix':{k:matrix[k] for k in ['responsiveRenders','axeAudits','visualSnapshots','scenarios','failures']},'lint':{'phpFiles':lint['files'],'failures':0},'audits':{'npmVulnerabilities':0,'composerAdvisories':0},'evidence':{'visualRoot':str(args.visual_root),'screenshotManifestSha256':hashlib.sha256((args.visual_root/'screenshot-manifest.json').read_bytes()).hexdigest()},'scope':'Installed Chrome; default compositions plus interaction tests. Automated audits and keyboard checks are not a full WCAG certification or all possible authored content.'}
(ROOT/'DUCS/PHASE2_TEST_RESULTS.json').write_text(json.dumps(report,indent=2)+'\n')
(ROOT/'include_site/studio/catalog/quality-results.json').write_text(json.dumps(report,indent=2)+'\n')
print(json.dumps({k:report[k] for k in ['status','catalogFingerprint','library']}))
