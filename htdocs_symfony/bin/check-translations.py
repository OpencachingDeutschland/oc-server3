#!/usr/bin/env python3
"""
Check all translation YAML files for duplicate keys.
Usage: python3 bin/check-translations.py
Exit code 1 if duplicates found (suitable for pre-commit hooks or CI).
"""
import re
import sys
import glob
import os

def check_file(path):
    seen = {}
    duplicates = []
    with open(path) as f:
        for i, line in enumerate(f, 1):
            m = re.match(r"^(['\"]?)([^'\":#\n]+)\1\s*:", line)
            if m:
                key = m.group(2).strip()
                if key in seen:
                    duplicates.append((i, seen[key], key))
                else:
                    seen[key] = i
    return duplicates

base = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
files = glob.glob(os.path.join(base, 'translations', '*.yaml'))

errors = 0
for path in sorted(files):
    dups = check_file(path)
    if dups:
        print(f"\n{os.path.basename(path)}:")
        for ln, first, key in dups:
            print(f"  DUPLICATE at line {ln} (first at {first}): '{key}'")
        errors += len(dups)

if errors:
    print(f"\n{errors} duplicate key(s) found. Fix before committing.")
    sys.exit(1)
else:
    print("All translation files OK — no duplicate keys.")
