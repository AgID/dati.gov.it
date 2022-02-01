#!/usr/bin/env bash
#1.0

# Check for correct params.
if [ $# -ne 2 ]; then
  echo "ERROR: Incorrect number of params. Must be SUBTHEME PATH (no trailing slash)."
  exit 1
fi

SUBTHEME=$1
SUBPATH=$2

# Check for existing theme.
if [ -d "$SUBPATH/$SUBTHEME" ]; then
  echo "ERROR: Directory already exists."
  exit 1
fi

if [ ! -d "$SUBPATH" ]; then
  mkdir -p $SUBPATH || echo "ERROR: Can't create directory." && exit 1
fi

cp -R _SUBTHEME $SUBPATH
mv $SUBPATH/_SUBTHEME $SUBPATH/$SUBTHEME
mv $SUBPATH/$SUBTHEME/SUBTHEME.libraries.yml $SUBPATH/$SUBTHEME/$SUBTHEME.libraries.yml
mv $SUBPATH/$SUBTHEME/SUBTHEME.theme $SUBPATH/$SUBTHEME/$SUBTHEME.theme
sed -i -e "s/SUBTHEME/$SUBTHEME/g" $SUBPATH/$SUBTHEME/SUBTHEME.info
sed -i -e "s/SUBTHEME/$SUBTHEME/g" $SUBPATH/$SUBTHEME/composer.json
mv $SUBPATH/$SUBTHEME/SUBTHEME.info $SUBPATH/$SUBTHEME/$SUBTHEME.info.yml
rm $SUBPATH/$SUBTHEME/*-e
