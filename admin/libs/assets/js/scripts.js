/*
 *
 *   start poolad project
 *   data 03/01/2014
 *   ui : omid khosrojerdi
 *
*/
$(document).ready(function(){
    $('.rightSideBar').css('height',$(document).height());

    $('.table').footable();
});

$(function(){

    // height of mainContainer - navbar height for fix positions
    var win_height = $(window).height();
    $('.rightSideBar').css('height',win_height-50);
    $('.mainContainer,.contentHolder > .tab-content').css('height',win_height-65);

});

// Settings object that controls default parameters for library methods:
accounting.settings = {
    currency: {
        symbol : "﷼",   // default currency symbol is '$'
        format: "%s %v", // controls output: %s = symbol, %v = value/number (can be object: see below)
        decimal : ".",  // decimal point separator
        thousand: ",",  // thousands separator
        precision : 2   // decimal places
    },
    number: {
        precision : 0,  // default precision on numbers is 0
        thousand: ",",
        decimal : "."
    }
}
