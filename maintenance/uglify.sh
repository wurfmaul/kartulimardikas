#!/bin/bash

### Use uglify-js (https://www.npmjs.com/package/uglify-js) to compress js files. ###
JS_PATH=../js
UGLIFY_JS_OPTIONS="-c -m --screw-ie8 --comments"
/usr/bin/uglifyjs ${JS_PATH}/{common,admin,table}.js ${UGLIFY_JS_OPTIONS} -o ${JS_PATH}/admin.min.js
/usr/bin/uglifyjs ${JS_PATH}/{common,section,autocomplete,algorithm,edit}.js ${UGLIFY_JS_OPTIONS} -o ${JS_PATH}/edit.min.js
/usr/bin/uglifyjs ${JS_PATH}/common.js ${UGLIFY_JS_OPTIONS} -o ${JS_PATH}/home.min.js
/usr/bin/uglifyjs ${JS_PATH}/{common,table}.js ${UGLIFY_JS_OPTIONS} -o ${JS_PATH}/index.min.js
/usr/bin/uglifyjs ${JS_PATH}/common.js ${UGLIFY_JS_OPTIONS} -o ${JS_PATH}/notice.min.js
/usr/bin/uglifyjs ${JS_PATH}/{common,register}.js ${UGLIFY_JS_OPTIONS} -o ${JS_PATH}/register.min.js
/usr/bin/uglifyjs ${JS_PATH}/{common,settings}.js ${UGLIFY_JS_OPTIONS} -o ${JS_PATH}/settings.min.js
/usr/bin/uglifyjs ${JS_PATH}/{common,user}.js ${UGLIFY_JS_OPTIONS} -o ${JS_PATH}/user.min.js
/usr/bin/uglifyjs ${JS_PATH}/{common,section,algorithm,view}.js ${UGLIFY_JS_OPTIONS} -o ${JS_PATH}/view.min.js

### Use uglify-css (https://www.npmjs.com/package/uglifycss) to compress css files. ###
CSS_PATH=../css
UGLIFY_CSS_OPTIONS="--cute-comments"
/usr/bin/uglifycss ${UGLIFY_CSS_OPTIONS} ${CSS_PATH}/{common,admin}.css > ${CSS_PATH}/admin.min.css
/usr/bin/uglifycss ${UGLIFY_CSS_OPTIONS} ${CSS_PATH}/{common,edit}.css > ${CSS_PATH}/edit.min.css
/usr/bin/uglifycss ${UGLIFY_CSS_OPTIONS} ${CSS_PATH}/common.css > ${CSS_PATH}/home.min.css
/usr/bin/uglifycss ${UGLIFY_CSS_OPTIONS} ${CSS_PATH}/{common,index}.css > ${CSS_PATH}/index.min.css
/usr/bin/uglifycss ${UGLIFY_CSS_OPTIONS} ${CSS_PATH}/common.css > ${CSS_PATH}/notice.min.css
/usr/bin/uglifycss ${UGLIFY_CSS_OPTIONS} ${CSS_PATH}/common.css > ${CSS_PATH}/register.min.css
/usr/bin/uglifycss ${UGLIFY_CSS_OPTIONS} ${CSS_PATH}/common.css > ${CSS_PATH}/settings.min.css
/usr/bin/uglifycss ${UGLIFY_CSS_OPTIONS} ${CSS_PATH}/common.css > ${CSS_PATH}/user.min.css
/usr/bin/uglifycss ${UGLIFY_CSS_OPTIONS} ${CSS_PATH}/{common,view}.css > ${CSS_PATH}/view.min.css
