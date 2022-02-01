$=jQuery;
$(document).ready(function() {
	$(".js-pager__items.pager").addClass("text-center");
	$(".js-pager__items.pager").addClass("p-0");
	$(".pager__item").css("list-style", "none");
	$(".pager__item a.button").addClass("btn");
	$(".pager__item a.button").addClass("btn-primary");
});

$(document).ajaxComplete(function() {
	$(".js-pager__items.pager").addClass("text-center");
        $(".js-pager__items.pager").addClass("p-0");
        $(".pager__item").css("list-style", "none");
        $(".pager__item a.button").addClass("btn");
        $(".pager__item a.button").addClass("btn-primary");
});
