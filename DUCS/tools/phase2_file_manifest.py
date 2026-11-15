#!/usr/bin/env python3
"""Compare application source/assets with the verified private Phase 2 file manifest."""
import argparse
import csv
import hashlib
import json
from pathlib import Path

ROOT=Path(__file__).resolve().parents[2]
parser=argparse.ArgumentParser()
parser.add_argument('--baseline', required=True, type=Path)
args=parser.parse_args()
baseline=json.loads(args.baseline.read_text())
def included(path):
    parts=Path(path).parts
    return not any(p in {'.git','node_modules','vendor','.phpunit.cache'} for p in parts) and path not in {'DUCS/PHASE2_FILES.tsv','DUCS/PHASE2_FILE_COMPARISON.json'} and not path.endswith(('.tsbuildinfo','.phpunit.result.cache'))
def fingerprint(path):
    if path.is_symlink(): return {'symlink': str(path.readlink())}
    return {'bytes':path.stat().st_size,'sha256':hashlib.sha256(path.read_bytes()).hexdigest()}
current={str(p.relative_to(ROOT)):fingerprint(p) for p in ROOT.rglob('*') if (p.is_file() or p.is_symlink()) and included(str(p.relative_to(ROOT)))}
rows=[]
for path in sorted(set(current)|{p for p in baseline if included(p)}):
    old=baseline.get(path);new=current.get(path)
    if old==new:continue
    rows.append({'status':'created' if old is None else 'removed' if new is None else 'modified','path':path,'bytes':new.get('bytes','') if new else '', 'sha256':new.get('sha256','') if new else ''})
with (ROOT/'DUCS/PHASE2_FILES.tsv').open('w') as output:
    writer=csv.DictWriter(output,fieldnames=['status','path','bytes','sha256'],delimiter='\t',lineterminator='\n');writer.writeheader();writer.writerows(rows)
result={'created':sum(r['status']=='created' for r in rows),'modified':sum(r['status']=='modified' for r in rows),'removed':[r['path'] for r in rows if r['status']=='removed'],'exclusions':'Git internals, installed node_modules/vendor dependencies, generated cache/tsbuildinfo and these two self-referential manifest files. Lockfiles, Vite deployment manifest and deployed bundles are included.'}
(ROOT/'DUCS/PHASE2_FILE_COMPARISON.json').write_text(json.dumps(result,indent=2)+'\n')
print(json.dumps(result))
