#!/usr/bin/env python3
"""Read-only Apache boundary checks; never persist response bodies or cookies."""
import json
import argparse
from pathlib import Path
import urllib.request
import urllib.error

class NoRedirect(urllib.request.HTTPRedirectHandler):
    def redirect_request(self, req, fp, code, msg, headers, newurl): return None

parser=argparse.ArgumentParser()
parser.add_argument('--root-vhost',action='store_true')
args=parser.parse_args()
base = 'http://127.0.0.1/' if args.root_vhost else 'http://127.0.0.1/expertsacademy/'
paths = {
    'site_admin/page_studio.php': 302,
    'site_admin/component_studio.php': 302,
    'site_admin/design_system.php': 302,
    'site_admin/studio_leads.php': 302,
    'site_admin/studio_api.php?resource=design-catalog': 401,
    'site_admin/studio_api.php?resource=leads': 401,
    'site_admin/studio_component_preview.php?demo=curated': 401,
    'site_admin/studio_preview.php': 401,
    'include_site/studio/contracts/page.v2.json': 403,
    'include_site/studio/database/002_design_studio.up.sql': 403,
    'site_admin/studio/package.json': 403,
    'DUCS/PHASE2_DESIGN_TOKENS.json': 403,
    '.git/HEAD': 403,
    'site.sql': 403,
    'studio_lead.php': 200,
    'studio_lead.php?token=1': 200,
    'asset_site/studio/design-system.css': 200,
    'asset_site/studio/previews/hero-editorial-split.webp': 200,
    'asset_site/studio/media/academy-preview.mp4': 200,
}
manifest=json.loads(Path('site_admin/asset/page-studio/.vite/manifest.json').read_text())
for entry in manifest.values():
    if entry.get('isEntry') or entry['file'].endswith(('.woff','.woff2')): paths['site_admin/asset/page-studio/'+entry['file']]=200
opener=urllib.request.build_opener(NoRedirect)
results=[]
for path,expected in paths.items():
    try:
        req=urllib.request.Request(base+path,headers={'Host':'expertsacademy.local'} if args.root_vhost else {})
        with opener.open(req,timeout=15) as response: status=response.status;response.read()
    except urllib.error.HTTPError as error: status=error.code;error.read()
    results.append({'path':path,'expected':expected,'observed':status,'passed':status==expected})
output={'checks':len(results),'failures':[r for r in results if not r['passed']],'results':results}
Path('DUCS/PHASE2_HTTP_ROOT_BOUNDARIES.json' if args.root_vhost else 'DUCS/PHASE2_HTTP_BOUNDARIES.json').write_text(json.dumps(output,indent=2)+'\n')
print(json.dumps({k:output[k] for k in ['checks','failures']}))
raise SystemExit(bool(output['failures']))
