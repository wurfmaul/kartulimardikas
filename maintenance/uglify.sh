#!/bin/bash

### Use uglify-js (https://www.npmjs.com/package/uglify-js) to compress js files. ###
JS_PATH=../js
JS_PATH_GEN=../js
UGLIFY_JS_OPTIONS="-c -m --screw-ie8 --comments"
uglifyjs ${JS_PATH}/{common,admin,table}.js ${UGLIFY_JS_OPTIONS} -o ${JS_PATH_GEN}/admin.min.js
uglifyjs ${JS_PATH}/{common,section,autocomplete,value,node,memory,tree,edit}.js ${UGLIFY_JS_OPTIONS} -o ${JS_PATH_GEN}/edit.min.js
uglifyjs ${JS_PATH}/common.js ${UGLIFY_JS_OPTIONS} -o ${JS_PATH_GEN}/home.min.js
uglifyjs ${JS_PATH}/{common,table}.js ${UGLIFY_JS_OPTIONS} -o ${JS_PATH_GEN}/index.min.js
uglifyjs ${JS_PATH}/common.js ${UGLIFY_JS_OPTIONS} -o ${JS_PATH_GEN}/new.min.js
uglifyjs ${JS_PATH}/common.js ${UGLIFY_JS_OPTIONS} -o ${JS_PATH_GEN}/notice.min.js
uglifyjs ${JS_PATH}/{common,register}.js ${UGLIFY_JS_OPTIONS} -o ${JS_PATH_GEN}/register.min.js
uglifyjs ${JS_PATH}/{common,settings}.js ${UGLIFY_JS_OPTIONS} -o ${JS_PATH_GEN}/settings.min.js
uglifyjs ${JS_PATH}/{common,user}.js ${UGLIFY_JS_OPTIONS} -o ${JS_PATH_GEN}/user.min.js
uglifyjs ${JS_PATH}/{common,section,value,node,memory,tree,view}.js ${UGLIFY_JS_OPTIONS} -o ${JS_PATH_GEN}/view.min.js

### Use uglify-css (https://www.npmjs.com/package/uglifycss) to compress css files. ###
CSS_PATH=../css
CSS_PATH_GEN=../css
UGLIFY_CSS_OPTIONS="--cute-comments"
uglifycss ${UGLIFY_CSS_OPTIONS} ${CSS_PATH}/{common,admin}.css > ${CSS_PATH_GEN}/admin.min.css
uglifycss ${UGLIFY_CSS_OPTIONS} ${CSS_PATH}/{common,edit}.css > ${CSS_PATH_GEN}/edit.min.css
uglifycss ${UGLIFY_CSS_OPTIONS} ${CSS_PATH}/common.css > ${CSS_PATH_GEN}/home.min.css
uglifycss ${UGLIFY_CSS_OPTIONS} ${CSS_PATH}/{common,index}.css > ${CSS_PATH_GEN}/index.min.css
uglifycss ${UGLIFY_CSS_OPTIONS} ${CSS_PATH}/common.css > ${CSS_PATH_GEN}/new.min.css
uglifycss ${UGLIFY_CSS_OPTIONS} ${CSS_PATH}/common.css > ${CSS_PATH_GEN}/notice.min.css
uglifycss ${UGLIFY_CSS_OPTIONS} ${CSS_PATH}/common.css > ${CSS_PATH_GEN}/register.min.css
uglifycss ${UGLIFY_CSS_OPTIONS} ${CSS_PATH}/common.css > ${CSS_PATH_GEN}/settings.min.css
uglifycss ${UGLIFY_CSS_OPTIONS} ${CSS_PATH}/common.css > ${CSS_PATH_GEN}/user.min.css
uglifycss ${UGLIFY_CSS_OPTIONS} ${CSS_PATH}/{common,view}.css > ${CSS_PATH_GEN}/view.min.css
