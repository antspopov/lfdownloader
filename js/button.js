b_text_1 = 'Включить загрузку';
b_text_2 = 'Выключить загрузку';

function button_reload(select, action){
    $(select).removeClass();
    $(select).addClass('btn btn-success ' + action);
    $(select).text(b_text_1);
}

function alert(text){
    $('#alert').alert('close');
    $('#alert-box').prepend('<div class="alert alert-danger" id="alert"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Ошибка!</strong> ' + text + '</div>')
}

function alert_run_download(type){
    $('#alert').alert('close');
    $('#alert-box').prepend('<div class="alert alert-success" id="alert"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Начинаю закачку и обновление '+ type +'а!</strong> Не забудьте отменить обновление перед удалением '+ type +'а с диска. Иначе он будет закачан снова.</div>')
}

function alert_stop_download(type){
    $('#alert').alert('close');
    $('#alert-box').prepend('<div class="alert alert-danger" id="alert"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Отмена обновления '+ type +'а!</strong> Этот '+ type +' больше не будет обновляться. Для удаления '+ type +'а, удалите его самостоятельно с диска.</div>')
}

//function status_serial(id_serial){
//    if (id_serial) {
//        var URL = "get_status.php?id=" + id_serial + "&type=serial"
//    }
//    else{
//        var URL = "get_status.php?type=serial"
//    }
//    $.ajax({
//        url: URL,
//        type: "GET",
//        dataType: "json",
//        success: function(data) {
//            for (var i = 0; i < data.length; i++) {
//                var id = data[i].ID;
//                var name = data[i].Name;
//                //alert(name);
//                if (data[i].DL == 0) {
//                    //alert('ok ' + name);
//                    $.ajax({
//                        url: "get_status.php?id=" + id + "&type=serial_e",
//                        type: "GET",
//                        async: false,
//                        dataType: "json",
//                        success: function(data2) {
//                            //alert('ok ' + name + data2 + data2.length);
//                            if (data2 == null) {
//                                $('div[name=status][id=' + id + ']').empty();
//                            }
//                            else {
//                                if ($('div[name=status][id=' + id + ']').html().indexOf('span') == -1) {
//                                    $('div[name=status][id=' + id + ']').prepend('<span name="status_serial" id="' + id + '" class="label label-default">Обновляется эпизодов <span class="badge">' + data2.length + '</span></span>');
//                                }
//                                else {
//                                    $('div[name=status][id=' + id + ']').html('<span name="status_serial" id="' + id + '" class="label label-default">Обновляется эпизодов <span class="badge">' + data2.length + '</span></span>');
//                                }
//                            }
//                        }
//                    });
//
//                }
//                else {
//                    if ($('div[name=status][id=' + id + ']').html().indexOf('span') == -1) {
//                        $('div[name=status][id=' + id + ']').prepend('<span name="status_serial" id="' + $(this).attr('id') + '" class="label label-primary">Идет загрузка...</span>');
//                    }
//                    else {
//                        $('div[name=status][id=' + id + ']').html('<span name="status_serial" id="' + $(this).attr('id') + '" class="label label-primary">Идет загрузка...</span>');
//                    }
//
//
//                }
//            }
//        }
//    });
//    //$('div[name=status]').each(function () {
//    //    var label = $(this);
//    //    $.get('get_status.php', {id:$(this).attr('id'), type: 'serial'}, function (data) {
//    //        $(this).prepend('<span name="status_serial" id="' + $(this).attr('id') + '" class="label label-default">Обновляется эпизодов <span class="badge">7</span></span>');
//    //    });
//    //});
//}
function status_serial(id_serial){
    if ( id_serial ){
        var button = 'button[name^=serial][id=' + id_serial + ']'
    }
    else
    {
        var button = 'button[name^=serial]'
    }
    $(button).each(function () {
        var id = $(this).attr('id');
        if ($(this).hasClass('btn-success')) {
            var count = $('button.btn-danger[name*=_' + id + '_]').size()
            if (count == 0) {
                $('div[name=status][id=' + id + ']').empty();
            }
            else {
                if ($('div[name=status][id=' + id + ']').html().indexOf('span') == -1) {
                    $('div[name=status][id=' + id + ']').prepend('<span name="status_serial" id="' + id + '" class="label label-default">Обновляется эпизодов <span class="badge">' + count + '</span></span>');
                }
                else {
                    $('div[name=status][id=' + id + ']').html('<span name="status_serial" id="' + id + '" class="label label-default">Обновляется эпизодов <span class="badge">' + count + '</span></span>');
                }
            }
        }
        else {
            $.ajax({
                url: "get_status.php?id=" + id + "&type=serial_s",
                type: "GET",
                dataType: "json",
                success: function(data2) {
                    //alert('ok ' + name + data2 + data2.length);
                    if (data2 == null) {
                        if ($('div[name=status][id=' + id + ']').html().indexOf('span') == -1) {
                            $('div[name=status][id=' + id + ']').prepend('<span name="status_serial" id="' + id + '" class="label label-warning">Получение статуса...</span>');
                        }
                        else {
                            $('div[name=status][id=' + id + ']').html('<span name="status_serial" id="' + id + '" class="label label-warning">Получение статуса...</span>');
                        }
                    }
                    else {
                        var dl = 0;
                        for (var i = 0; i < data2.length; i++) {
                            if (data2[i].DL == 1) {
                                dl = 1;
                            }
                        }
                        if (dl == 0) {
                            if ($('div[name=status][id=' + id + ']').html().indexOf('span') == -1) {
                                $('div[name=status][id=' + id + ']').prepend('<span name="status_serial" id="' + id + '" class="label label-success">Загружено</span>');
                            }
                            else {
                                $('div[name=status][id=' + id + ']').html('<span name="status_serial" id="' + id + '" class="label label-success">Загружено</span>');
                            }
                        }
                        else {
                            if ($('div[name=status][id=' + id + ']').html().indexOf('span') == -1) {
                                $('div[name=status][id=' + id + ']').prepend('<span name="status_serial" id="' + id + '" class="label label-primary">Идет загрузка...</span>');
                            }
                            else {
                                $('div[name=status][id=' + id + ']').html('<span name="status_serial" id="' + id + '" class="label label-primary">Идет загрузка...</span>');
                            }
                        }
                    }
                }
            });

        }
    });

}

function status_episode() {
    $.ajax({
        url: "get_status.php?type=episode",
        type: "GET",
        dataType: "json",
        success: function(data) {
            if (data != null) {
                for (var i = 0; i < data.length; i++) {
                    var id = data[i].ID;
                    var status = data[i].DL;
                    var name_quality = data[i].Quality;
                    if (status == 1) {
                        if ($('td[name=status][id=' + id + ']').html().indexOf('span') == -1) {
                            $('td[name=status][id=' + id + ']').prepend('<span name="status" id="' + id + '" class="label label-primary">Идет загрузка...</span><br><span name="quality" id="' + id + '" class="label label-default">' + name_quality + '</span>');
                        }
                        else {
                            $('td[name=status][id=' + id + ']').html('<span name="status" id="' + id + '" class="label label-primary">Идет загрузка...</span><br><span name="quality" id="' + id + '" class="label label-default">' + name_quality + '</span>');
                        }
                    }
                    else {
                        if ($('td[name=status][id=' + id + ']').html().indexOf('span') == -1) {
                            $('td[name=status][id=' + id + ']').prepend('<span name="status" id="' + id + '" class="label label-success">Загружено</span><br><span name="quality" id="' + id + '" class="label label-default">' + name_quality + '</span>');
                        }
                        else {
                            $('td[name=status][id=' + id + ']').html('<span name="status" id="' + id + '" class="label label-success">Загружено</span><br><span name="quality" id="' + id + '" class="label label-default">' + name_quality + '</span>');
                        }
                    }
                }
            }
        }
    });
}

$(document).ready(function(){
    $('button.active[name=serial]').click(function () {
        $.get('get_dl.php',{id:$(this).attr('id'), type:$(this).attr('name')})
        if ( $(this).text() == b_text_1 ){
            $(this).fadeOut(300, function(){$(this).text(b_text_2)});
            $(this).fadeIn(300);
            $(this).removeClass();
            $(this).addClass('btn btn-danger active');
            button_reload('button[name*=_' + $(this).attr('id') + '_]', 'disabled');
            alert_run_download('сериал');
            status_serial($(this).attr('id'));
        }
        else{
            $(this).fadeOut(300, function(){$(this).text(b_text_1)});
            $(this).fadeIn(300);
            $(this).removeClass();
            $(this).addClass('btn btn-success active');
            button_reload('button[name*=_' + $(this).attr('id') + '_]', 'active');
            alert_stop_download('сериал');
            status_serial($(this).attr('id'));
        }
    });
    $('button[name^=season_]').click(function () {
        if ( $(this).hasClass('active') ){
            $.get('get_dl.php',{id:$(this).attr('id'), type:$(this).attr('name')})
            if ( $(this).text() == b_text_1 ){
                $(this).fadeOut(300, function(){$(this).text(b_text_2)});
                $(this).fadeIn(300);
                $(this).removeClass();
                $(this).addClass('btn btn-danger active');
                if ( $('#' + $(this).attr('id')).html().indexOf('span') == -1 ) {
                    $('#' + $(this).attr('id')).prepend('<span  name="status" id="' + $(this).attr('id') + '" class="label label-warning">Получение статуса...</span><br><span  name="quality" id="' + $(this).attr('id') + '" class="label label-default"></span>')
                }
                button_reload('button[name=episode_' + $(this).attr('name').split("_")[1] + '_' + $(this).attr('name').split("_")[2] + ']', 'disabled');
                alert_run_download('сезон');
                status_serial($(this).attr('name').split("_")[1]);
                status_episode($(this).attr('id'))
            }
                else{
                $(this).fadeOut(300, function(){$(this).text(b_text_1)});
                $(this).fadeIn(300);
                $(this).removeClass();
                $(this).addClass('btn btn-success active');
                button_reload('button[name=episode_' + $(this).attr('name').split("_")[1] + '_' + $(this).attr('name').split("_")[2] + ']', 'active');
                alert_stop_download('сезон');
                status_serial($(this).attr('name').split("_")[1]);
                status_episode($(this).attr('id'))
            }
        }
    });
        $('button[name^=episode_]').click(function () {
        if ( $(this).hasClass('active') ){
            $.get('get_dl.php',{id:$(this).attr('id'), type:$(this).attr('name')})
            if ( $(this).text() == b_text_1 ){
                $(this).fadeOut(300, function(){$(this).text(b_text_2)});
                $(this).fadeIn(300);
                $(this).removeClass();
                $(this).addClass('btn btn-danger active');
                if ( $('#' + $(this).attr('id')).html().indexOf('span') == -1 ) {
                    $('#' + $(this).attr('id')).prepend('<span name="status" id="' + $(this).attr('id') + '" class="label label-warning">Получение статуса...</span><br><span  name="quality" id="' + $(this).attr('id') + '" class="label label-default"></span>')
                }
                alert_run_download('эпизод');
                status_serial($(this).attr('name').split("_")[1]);
                status_episode($(this).attr('id'))
            }
            else{
                $(this).fadeOut(300, function(){$(this).text(b_text_1)});
                $(this).fadeIn(300);
                $(this).removeClass();
                $(this).addClass('btn btn-success active');
                alert_stop_download('эпизод');
                status_serial($(this).attr('name').split("_")[1]);
                status_episode($(this).attr('id'))
            }
        }
    });
    status_serial();
    status_episode();
    setInterval(function () {
        status_episode();
        status_serial();
    }, 30000);
});

