# build model

if [ "$#" -ne 2 ]; then
	echo Usage: ./build.sh prod compact or ./build.sh dev pretty
fi

# compile the js files with google closure compiler
echo "compile js with google closure compiler"
python compilejs.py $1 $2 >html/min.js

# concatenate and compile the css files
echo "minify the css files"
cat html/minimal/normaleyes.css html/minimal/minimal.css html/minimal/theme/mahagony.css html/icon/icon.css html/css/model.css | 
    sed 's/+/%2b/g'  >html/min.css
wget --post-data="input=`cat html/min.css`" --output-document=html/min.css https://cssminifier.com/raw

# prepare index.php for production use
echo "fix index.php"
cp html/index.html html/index.php
sed -i -e 's/<!--<remove>//g' html/index.php
sed -i -e 's/<remove>-->//g' html/index.php
echo "complete"
