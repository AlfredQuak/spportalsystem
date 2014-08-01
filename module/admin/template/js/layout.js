$(document).ready(function() {

    var oldMouseX = 0;
    var oldMouseY = 0;
    var resizing = false;

    /*$('#sp_console').click(function() {
        $(this).toggle('clip', '', 500);
        return false;
    });*/

    $('#sp_console_button').click(function() {
        $('#sp_console').toggle('clip', '', 500);
        return false;
    });

    $('body').on('mousedown', 'header .resizer', function(e) {
        if (!resizing) {
            e.preventDefault();
            $('body').bind('mousemove', resizeVertical);
            $('body').bind('mouseup', stopVerticalResize);
            oldMouseX = e.pageX;
            resizing = true;
        }
    });

    function resizeVertical(e) {
        var naviTop = parseInt($('header ul').css("top"));
        var mouseY = e.pageY - naviTop;
        var newPos = mouseY;
        $('header .level2').css("height", newPos + "px");
        $('header .level1 div').css("bottom", "-20px");
        document.body.style.cursor = 'n-resize';
        oldMouseY = e.pageY;
    }

    function stopVerticalResize(e) {
        $('body').unbind('mousemove', resizeVertical);
        $('body').unbind('mouseup', stopVerticalResize);
        document.body.style.cursor = 'auto';
        if ($('header .level2').height() > 50) {
            $('header .level2').animate({"height": "50px"});
        } else {
            $('header .level2').animate({"height": "0px"}, function() {
                $('header .level1 div').animate({"bottom": "4px"}, 50, "linear");
            });
        }
        resizing = false;
    }

    //Horizontal Resize
    $('body').on('mousedown', 'section .resizer', function(e) {
        e.preventDefault();
        $('body').bind('mousemove', resizeHorizontal);
        $('body').bind('mouseup', stopHorizontalResize);
        oldMouseX = e.pageX;
    });

    $('body').on('click', 'a.button', function(e) {
        $('#main .left').animate({"width": "50%"}, 1000);
        $('#main .right').height($('#main').height()).animate({"width": "50%", "padding": "20px"}, 1000);

    });

    function resizeHorizontal(e) {
        var mainWidth = $('#main .inner').width();
        var mouseX = e.pageX - 220;
        var newPos = mouseX / mainWidth * 100;
        $('#main .left').css("width", newPos + "%");
        $('#main .right').css("width", (100 - newPos) + "%");
        document.body.style.cursor = 'e-resize';

        if (oldMouseX - e.pageX < -40 || mouseX > mainWidth - 40) {
            $('body').unbind('mousemove', resizeHorizontal);
            document.body.style.cursor = 'auto';
            $('#main .left').animate({"width": "100%"}, 1000);
            $('#main .right').animate({"width": "0%", "padding": "20px 0"}, 1000, function() {

            });
        }

        if (oldMouseX - e.pageX > 40 || (mouseX < 40 && oldMouseX - e.pageX > 0)) {
            $('body').unbind('mousemove', resizeHorizontal);
            $('body').unbind('mouseup', stopHorizontalResize);
            document.body.style.cursor = 'auto';
            $('#main .left').animate({"width": "0%", "padding": "20px 0"}, 1000, function() {

            });
            $('#main .right').animate({"width": "100%"}, 1000);
        }

        oldMouseX = e.pageX;
    }

    function stopHorizontalResize(e) {
        $('body').unbind('mousemove', resizeHorizontal);
        document.body.style.cursor = 'auto';
    }

});