#!/usr/bin/env python3
"""Check translation YAML files for duplicate keys.

Symfony's YAML loader treats duplicate keys as a hard error,
which causes HTTP 500 on every page that loads the translation domain.

Usage:
  python3 scripts/check-yaml-duplicates.py          # check all translation files
  python3 scripts/check-yaml-duplicates.py --fix    # show what to remove (manual step)
"""

import re
import sys
from collections import Counter
from pathlib import Path

TRANSLATION_DIR = Path(__file__).resolve().parent.parent / "htdocs_symfony" / "translations"


def find_duplicates(filepath):
    """Return list of (key, count) tuples for duplicate top-level YAML keys."""
    with open(filepath) as f:
        lines = f.readlines()

    keys = []  # (key, line_number, raw_line)
    for i, line in enumerate(lines, 1):
        stripped = line.strip()
        if not stripped or stripped.startswith("#"):
            continue
        # Quoted key: 'key': or "key":
        m = re.match(r"""^(['"])(.+?)\1\s*:""", stripped)
        if m:
            keys.append((m.group(2), i, line))
            continue
        # Unquoted key: key:
        m = re.match(r"^([^'\"#][^:]*?)\s*:", stripped)
        if m:
            k = m.group(1).strip()
            keys.append((k, i, line))

    key_counts = Counter(k for k, _, _ in keys)
    dups = [(k, c) for k, c in key_counts.items() if c > 1]

    if dups:
        for key, count in dups:
            locations = [(ln, raw.rstrip()) for k, ln, raw in keys if k == key]
            print(f"\n  DUPLICATE: '{key}' ({count}x)")
            for ln, raw in locations:
                print(f"    line {ln}: {raw}")

    return dups


def main():
    args = set(sys.argv[1:])
    yaml_files = sorted(TRANSLATION_DIR.glob("messages+intl-icu.*.yaml"))

    if not yaml_files:
        print(f"No translation files found in {TRANSLATION_DIR}")
        sys.exit(1)

    total_dups = 0
    for fpath in yaml_files:
        print(f"Checking {fpath.name}...", end="")
        dups = find_duplicates(fpath)
        if dups:
            total_dups += len(dups)
            print(f" {len(dups)} duplicate(s)")
        else:
            print(" clean")

    if total_dups:
        print(f"\n❌ {total_dups} duplicate key(s) found. Fix before committing.")
        print("   Each duplicate will cause a YAML parse error at runtime.")
        sys.exit(1)
    else:
        print("\n✓ All translation files clean.")


if __name__ == "__main__":
    main()
