+(function ($) {
  $(function () {
    // input class to use the datepicker function
    $(".date-pick").each(function () {
      $(this).datepicker();
    });

    // set custom colors to match theme/design
    //var colors = ['#000000', '#444444', '#666666', '#999999', '#cccccc', '#eeeeee', '#f3f3f3', '#ffffff'];
    //$('input.color').simpleColorPicker({ colors: colors });

    // use default colors
    $("input.color").simpleColorPicker();
  });
})(jQuery);
