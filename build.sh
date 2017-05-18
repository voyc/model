# build account

if [ "$#" -ne 2 ]; then
	echo Usage: ./build.sh prod compact or ./build.sh dev pretty
fi

# compile the js files with google closure compiler
python compilejs.py $1 $2 >html/min.js

# concatenate and compile the css files
cat html/normalize.css/normalize.css html/minimal/minimal.css html/minimal/theme/mahagony.css html/icon/icon.css html/css/account.css >html/min.css
java -jar /home/jhagstrand/bin/yuicompressor/yuicompressor-2.4.2.jar html/min.css -o html/min.css --charset utf-8

# prepare index.php for production use
cp html/index.html html/index.php
sed -i -e 's/<!--<remove>//g' html/index.php
sed -i -e 's/<remove>-->//g' html/index.php
