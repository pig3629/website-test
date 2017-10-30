
function O(i) { 
    return typeof i == 'object' ? i : document.getElementById(i)
    //將物件傳入，只會回傳物件
}
function S(i) { 
    return O(i).style
}
function C(i){
    return document.getElementsByClassName(i)
}
