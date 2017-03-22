$(document).ready(function(){
	$.fn.nav_slide = function(first,second){
        //移除页面上所有类名为active的class
        //$('.active').removeClass();
        $(this).attr('class','active');
        $('#'+first+'>ul').addClass('in');
        if (second != null) {
            $('#'+second).attr('class','active');
        }
    }
    $.fn.rowspan = function(colIdx) { //封装的一个JQuery小插件 
        return this.each(function(){ 
            var that; 
            $('tr', this).each(function(row) { 
                $('td:eq('+colIdx+')', this).filter(':visible').each(function(col) { 
                    if (that!=null && $(this).html() == $(that).html()) { 
                        rowspan = $(that).attr("rowSpan"); 
                        if (rowspan == undefined) { 
                            $(that).attr("rowSpan",1); 
                            rowspan = $(that).attr("rowSpan"); 
                        } 
                        rowspan = Number(rowspan)+1; 
                        $(that).attr("rowSpan",rowspan);
                        $(that).css('vertical-align','middle'); 
                        $(this).hide(); 
                    } else { 
                        that = this;
                    } 
                 }); 
            }); 
        }); 
    }
});