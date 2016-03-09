/**
 * 获取字符串实际的长度，中文2，英文1
 * @param {type} str
 * @example getLength('测试abcd') = 8；
 * @returns {Number}
 */
function getLength (str) {
    var len = str.length;
    var length = 0;
    for (var i = 0; i < len; i++) {
        if (str.charCodeAt(i) >= 0 && str.charCodeAt(i) <= 128) length += 1;
        else length += 2;
    }
    return length;
}

