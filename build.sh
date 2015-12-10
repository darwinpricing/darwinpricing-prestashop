#!/bin/bash
set -e
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
ZIP="$( dirname "${DIR}" )"/darwinpricing-prestashop.zip
rm -f ${ZIP}
cd $TMPDIR
rm -rf darwinpricing-prestashop
mkdir darwinpricing-prestashop
cd darwinpricing-prestashop
cp -r ${DIR} ./
mv darwinpricing-prestashop darwinpricing
rm -rf darwinpricing/.git darwinpricing/.gitignore darwinpricing/nbproject darwinpricing/build.sh darwinpricing/.DS_Store
zip -r -X ${ZIP} darwinpricing
echo Created ${ZIP}
