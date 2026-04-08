/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

"use strict";
// for pos system
var session_key = $(location).attr("href").split('/').pop();
//

$(function () {
    if ($('.custom-scroll').length) {
        $(".custom-scroll").niceScroll();
        $(".custom-scroll-horizontal").niceScroll();
    }


    // loadConfirm();

});

function validation() {

    var forms = document.querySelectorAll('.needs-validation');

    Array.prototype.forEach.call(forms, function (form) {

        form.addEventListener('submit', function (event) {
            var submitButton = form.querySelector('button[type="submit"], input[type="submit"]');

            if (submitButton) {
                submitButton.disabled = true;
            }
            if (form.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
                if (submitButton) {
                    submitButton.disabled = false;
                }
            }

            form.classList.add('was-validated');
        }, false);
    });
}

// Import Data for Dynamic function
function SetDynamicData(tbody, thead, count = 0) {
    if (count < 8) {
        var process_area = document.getElementById("process_area");
        if (process_area) {
            var theadHtml = '<tr>';
            for (let i = 0; i < Object.values(thead).length; i++) {
                theadHtml += '<th>';
                theadHtml += '<select name="set_column_data" class="form-control set_column_data" data-column_number="' + i + '">';
                theadHtml += '<option value="">Select Field</option>';
                $.each(thead, function(key, value) {
                    let formattedText = value.replace(/_/g, ' ').replace(/\b\w/g, function(letter) {
                        return letter.toUpperCase();
                    });
                    theadHtml += '<option value="' + value + '">' + formattedText + '</option>';
                });
                theadHtml += '</select>';
                theadHtml += '</th>';
            }
            theadHtml += '</tr>';

            $('#process_area .table .thead').html(theadHtml);
            $('#process_area .table .tbody').html(tbody);
        }
        else {
            setTimeout(function () {
                SetDynamicData(tbody, count + 1);
            }, 500);
        }
    }
    else {
        show_toastr('Success', '{{ __("Something went wrong, please try again!") }}', 'success');
    }
}

// Import Data
function SetData(params, count = 0) {
    if (count < 8) {
        var process_area = document.getElementById("process_area");
        if (process_area) {
            $('#process_area').html(params);
        }
        else {
            setTimeout(function () {
                SetData(params, count + 1);
            }, 500);
        }
    }
    else {
        toastrs('Success', '{{ __("Something went wrong, please try again!") }}', 'success');
    }
}

$(document).ready(function () {
    if ($(".datatable").length > 0) {
        const dataTable =  new simpleDatatables.DataTable(".datatable");
    }

    select2();
    summernote();
    daterange();
    // loadConfirm();
});

$(document).ready(function () {
    if ($(".needs-validation").length > 0) {
        validation();
    }

    if ($(".pc-dt-simple").length > 0) {
        $( $(".pc-dt-simple") ).each(function( index,element ) {
            var id = $(element).attr('id');
            const dataTable = new simpleDatatables.DataTable("#"+id);
        });
    }


    select2();
    summernote();
    daterange();
});


function daterange() {
    if ($("#pc-daterangepicker-1").length > 0) {
        document.querySelector("#pc-daterangepicker-1").flatpickr({
            mode: "range"
        });
    }
}


function select2() {
    /**
     * 공통 Select 검색/스타일 적용 (Choices.js)
     * - 대상: .select2, select.select
     * - ID가 없는 select 는 자동으로 고유 ID를 부여
     * - 한 번 초기화된 요소는 data-attributes 로 중복 초기화 방지
     */

    function initChoices($elements) {
        if (!$elements || !$elements.length) {
            return;
        }

        $elements.each(function (index, element) {
            var $el = $(element);

            // 이미 초기화된 경우 스킵
            if ($el.data('choices-initialized')) {
                return;
            }

            // ID 없으면 자동 생성
            var id = $el.attr('id');
            if (!id) {
                id = 'select-' + Math.random().toString(36).substr(2, 9);
                $el.attr('id', id);
            }

            var isMultiple = typeof $el.attr('multiple') !== 'undefined';

            try {
                new Choices('#' + id, {
                    removeItemButton: isMultiple,
                    searchEnabled: true,
                    shouldSort: false
                });

                $el.data('choices-initialized', true);
            } catch (e) {
                console && console.error && console.error('Choices init error on #' + id, e);
            }
        });
    }

    // 기존 .select2 유지
    if ($('.select2').length > 0) {
        initChoices($('.select2'));
    }

    // 프로젝트 전역 select 에 검색 기능 부여
    if ($('select.select').length > 0) {
        initChoices($('select.select'));
    }
}

function show_toastr(type, message) {

    var f = document.getElementById('liveToast');
    var a = new bootstrap.Toast(f).show();
    if (type == 'success') {
        $('#liveToast').addClass('bg-primary');
    } else {
        $('#liveToast').addClass('bg-danger');
    }
    $('#liveToast .toast-body').html(message);
}

$(document).on('click', 'a[data-ajax-popup="true"], button[data-ajax-popup="true"], div[data-ajax-popup="true"]', function () {

    var data = {};
    var title1 = $(this).data("title");

    var title2 = $(this).data("bs-original-title");
    var title3 = $(this).data("original-title");
    var title = (title1 != undefined) ? title1 : title2;
    var title=(title != undefined) ? title : title3;

    $('#commonModal .modal-dialog').removeClass('modal-xl modal-sm modal-md modal-lg modal-xxl');
    var size = ($(this).data('size') == '') ? 'md' : $(this).data('size');

    var url = $(this).data('url');
    $("#commonModal .modal-title").html(title);
    $("#commonModal .modal-dialog").addClass('modal-' + size);

    if ($('#vc_name_hidden').length > 0) {
        data['vc_name'] = $('#vc_name_hidden').val();
    }
    if ($('#warehouse_name_hidden').length > 0) {
        data['warehouse_name'] = $('#warehouse_name_hidden').val();
    }
    if ($('#discount_hidden').length > 0) {
        data['discount'] = $('#discount_hidden').val();
    }
    $.ajax({
        url: url,
        data: data,
        success: function (data) {
            $('#commonModal .body').html(data);
            $("#commonModal").modal('show');
            // daterange_set();
            taskCheckbox();
            common_bind("#commonModal");
            validation();
            commonLoader();

        },
        error: function (data) {
            data = data.responseJSON;
            show_toastr('Error', data.error, 'error')
        }
    });

});


function arrayToJson(form) {
    var data = $(form).serializeArray();
    var indexed_array = {};

    $.map(data, function (n, i) {
        indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
}


function common_bind() {
    select2();
}


function taskCheckbox() {
    var checked = 0;
    var count = 0;
    var percentage = 0;

    count = $("#check-list input[type=checkbox]").length;
    checked = $("#check-list input[type=checkbox]:checked").length;
    percentage = parseInt(((checked / count) * 100), 10);
    if (isNaN(percentage)) {
        percentage = 0;
    }
    $(".custom-label").text(percentage + "%");
    $('#taskProgress').css('width', percentage + '%');


    $('#taskProgress').removeClass('bg-warning');
    $('#taskProgress').removeClass('bg-primary');
    $('#taskProgress').removeClass('bg-success');
    $('#taskProgress').removeClass('bg-danger');

    if (percentage <= 15) {
        $('#taskProgress').addClass('bg-danger');
    } else if (percentage > 15 && percentage <= 33) {
        $('#taskProgress').addClass('bg-warning');
    } else if (percentage > 33 && percentage <= 70) {
        $('#taskProgress').addClass('bg-primary');
    } else {
        $('#taskProgress').addClass('bg-success');
    }
}


function commonLoader() {
    $('[data-toggle="tooltip"]').tooltip();
    if ($('[data-toggle="tags"]').length > 0) {
        $('[data-toggle="tags"]').tagsinput({tagClass: "badge badge-primary"});
    }


    // $(function(){
    //
    //     var dtToday = new Date();
    //
    //     var month = dtToday.getMonth() + 1;
    //     var day = dtToday.getDate();
    //     var year = dtToday.getFullYear();
    //     if(month < 10)
    //         month = '0' + month.toString();
    //     if(day < 10)
    //         day = '0' + day.toString();
    //
    //     var maxDate = year + '-' + month + '-' + day;
    //
    //     $("input[type='date']").attr('max', maxDate);
    // });

    var e = $(".scrollbar-inner");
    e.length && e.scrollbar().scrollLock()

    var e1 = $(".custom-input-file");
    e1.length && e1.each(function () {
        var e1 = $(this);
        e1.on("change", function (t) {
            !function (e, t, a) {
                var n, o = e.next("label"), i = o.html();
                t && t.files.length > 1 ? n = (t.getAttribute("data-multiple-caption") || "").replace("{count}", t.files.length) : a.target.value && (n = a.target.value.split("\\").pop()), n ? o.find("span").html(n) : o.html(i)
            }(e1, this, t)
        }), e1.on("focus", function () {
            !function (e) {
                e.addClass("has-focus")
            }(e1)
        }).on("blur", function () {
            !function (e) {
                e.removeClass("has-focus")
            }(e1)
        })
    })

    // var e2 = $('[data-toggle="autosize"]');
    // e2.length && autosize(e2);


    if ($(".jscolor").length) {
        jscolor.installByClassName("jscolor");
    }
    summernote();
    // for Choose file
    $(document).on('change', 'input[type=file]', function () {
        var fileclass = $(this).attr('data-filename');
        var finalname = $(this).val().split('\\').pop();
        $('.' + fileclass).html(finalname);
    });
}



function summernote() {
    if ($(".summernote-simple").length) {
        $('.summernote-simple').summernote({
            placeholder: "Write Here… ",
            tabsize: 2,
            minHeight: 200,
            maxHeight: 250,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'strikethrough']],
                ['list', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'unlink']],
            ],
            height: 250,
});
        $('.dropdown-toggle').dropdown();
    }

    if ($(".summernote-simple-2").length) {
        $('.summernote-simple-2').summernote({
            placeholder: "Write Here… ",
            dialogsInBody: !0,
            minHeight: 200,
            maxHeight: 250,
            toolbar: [
                ['style', ['style']],
                ["font", ["bold", "italic", "underline", "clear", "strikethrough"]],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ["para", ["ul", "ol", "paragraph"]],
            ],

        });
    }

}



$(document).on("click", '.bs-pass-para', function () {
    var form = $(this).closest("form");
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })
    swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        text: "This action can not be undone. Do you want to continue?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {

            form.submit();

        } else if (
            result.dismiss === Swal.DismissReason.cancel
        ) {
        }
    })
});

//only pos system delete button
$(document).on("click", '.bs-pass-para-pos', function () {

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })
    swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        text: "This action can not be undone. Do you want to continue?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {

            document.getElementById($(this).data('confirm-yes')).submit();

        } else if (
            result.dismiss === Swal.DismissReason.cancel
        ) {
        }
    })
});


function postAjax(url, data, cb) {
    var token = $('meta[name="csrf-token"]').attr('content');
    var jdata = {_token: token};

    for (var k in data) {
        jdata[k] = data[k];
    }

    $.ajax({
        type: 'POST',
        url: url,
        data: jdata,
        success: function (data) {
            if (typeof (data) === 'object') {
                cb(data);
            } else {
                cb(data);
            }
        },
    });
}

//end only pos system delete button


function deleteAjax(url, data, cb) {
    var token = $('meta[name="csrf-token"]').attr('content');
    var jdata = {_token: token};

    for (var k in data) {
        jdata[k] = data[k];
    }

    $.ajax({
        type: 'DELETE',
        url: url,
        data: jdata,
        success: function (data) {
            if (typeof (data) === 'object') {
                cb(data);
            } else {
                cb(data);
            }
        },
    });
}

// Google calendar
$(document).on('click', '.local_calender .fc-daygrid-event, .fc-timegrid-event', function (e) {
    // if (!$(this).hasClass('project')) {
    e.preventDefault();
    var event = $(this);
    var title1 = $('.fc-event-title').html();
    var title2 = $(this).data("bs-original-title");
    var title = (title1 != undefined) ? title1 : title2;
    // var size = ($(this).data('size') == '') ? 'md' : $(this).data('size');
    var size = 'md';
    var url = $(this).attr('href');
    $("#commonModal .modal-title").html(title);
    $('#commonModal .modal-dialog').removeClass('modal-xl modal-sm modal-md modal-lg modal-xxl');
    $("#commonModal .modal-dialog").addClass('modal-' + size);
    $.ajax({
        url: url,
        success: function (data) {
            $('#commonModal .body').html(data);
            $("#commonModal").modal('show');
            common_bind();
        },
        error: function (data) {
            data = data.responseJSON;
            toastrs('Error', data.error, 'error')
        }
    });
    // }
});

//date value 4

// $(function(){
//
//     var dtToday = new Date();
//
//     var month = dtToday.getMonth() + 1;
//     var day = dtToday.getDate();
//     var year = dtToday.getFullYear();
//     if(month < 10)
//         month = '0' + month.toString();
//     if(day < 10)
//         day = '0' + day.toString();
//
//     var maxDate = year + '-' + month + '-' + day;
//
//     $("input[type='date']").attr('max', maxDate);
// });

function addCommas(num) {
    var number = parseFloat(num).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    return ((site_currency_symbol_position == "pre") ? site_currency_symbol : '') + number + ((site_currency_symbol_position == "post") ? site_currency_symbol : '');
}



// PLUS MINUS QUANTITY JS
function wcqib_refresh_quantity_increments() {
    jQuery("div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)").each(function (a, b) {
        var c = jQuery(b);
        c.addClass("buttons_added"),
            c.children().first().before('<input type="button" value="-" class="minus" />'),
            c.children().last().after('<input type="button" value="+" class="plus" />')
    })
}

String.prototype.getDecimals || (String.prototype.getDecimals = function () {
    var a = this,
        b = ("" + a).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
    return b ? Math.max(0, (b[1] ? b[1].length : 0) - (b[2] ? +b[2] : 0)) : 0
}), jQuery(document).ready(function () {
    wcqib_refresh_quantity_increments()
}), jQuery(document).on("updated_wc_div", function () {
    wcqib_refresh_quantity_increments()
}), jQuery(document).on("click", ".plus, .minus", function () {
    var a = jQuery(this).closest(".quantity").find('input[name="quantity"], input[name="quantity[]"]'),
        b = parseFloat(a.val()),
        c = parseFloat(a.attr("max")),
        d = parseFloat(a.attr("min")),
        e = a.attr("step");
    b && "" !== b && "NaN" !== b || (b = 0), "" !== c && "NaN" !== c || (c = ""), "" !== d && "NaN" !== d || (d = 0), "any" !== e && "" !== e && void 0 !== e && "NaN" !== parseFloat(e) || (e = 1), jQuery(this).is(".plus") ? c && b >= c ? a.val(c) : a.val((b + parseFloat(e)).toFixed(e.getDecimals())) : d && b <= d ? a.val(d) : b > 0 && a.val((b - parseFloat(e)).toFixed(e.getDecimals())), a.trigger("change")
});

$(document).on('click', 'input[name="quantity"], input[name="quantity[]"]', function (e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
        // Allow: Ctrl+A
        (e.keyCode == 65 && e.ctrlKey === true) ||
        // Allow: home, end, left, right
        (e.keyCode >= 35 && e.keyCode <= 39)) {
        // let it happen, don't do anything
        return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }
});


//for ai module
$(document).on('click', 'a[data-ajax-popup-over="true"], button[data-ajax-popup-over="true"], div[data-ajax-popup-over="true"]', function () {
    var validate = $(this).attr('data-validate');
    var id = '';
    if (validate) {
        id = $(validate).val();
    }
    var title_over = $(this).data('title');
    $('#commonModalOver .modal-dialog').removeClass('modal-xl modal-sm modal-md modal-lg modal-xxl');
    var size_over = ($(this).data('size') == '') ? 'md' : $(this).data('size');

    var url = $(this).data('url');
    $("#commonModalOver .modal-title").html(title_over);
    $("#commonModalOver .modal-dialog").addClass('modal-' + size_over);
    $.ajax({
        url: url + '?id=' + id,
        success: function (data) {
            $('#commonModalOver .modal-body').html(data);
            $("#commonModalOver").modal('show');
            taskCheckbox();
        },
        error: function (data) {
            data = data.responseJSON;
            show_toastr('Error', data.error, 'error')
        }
    });
});



//start input serach box
function JsSearchBox() {
    if ($(".js-searchBox").length)
    {
        $( ".js-searchBox" ).each(function( index ) {
            if($(this).parent().find('.formTextbox').length == 0)
            {
                $(this).searchBox({ elementWidth: '250'});
            }
        });
    }
}

$(document).ready(function() {
    JsSearchBox();

    function JsSearchBox() {
        if ($(".js-searchBox").length) {
            $(".js-searchBox").each(function(index) {
                if ($(this).parent().find('.formTextbox').length === 0) {
                    $(this).searchBox({ elementWidth: '250' });
                }
            });
        }
    }
});

//end input serach box

//file validation
$(document).ready(function() {
    $(document).on("change",".file-validate",function(){
        let file_input = $(this)[0];
        let max_size = file_size;
        let allowed_extensions = file_types;
        let file_error = $('.file-error');
        file_error.text('');
        if (file_input.files.length > 0) {
            let file = file_input.files[0];
            let file_size = file.size / 1024;
            let file_extension = file.name.split('.').pop().toLowerCase();
            let extensions_array = allowed_extensions.split(',');
            if (!extensions_array.includes(file_extension)) {
                $(this).next().text(type_err);
                file_input.value = '';
                // document.getElementById('blah').src = '';
                return false;
            } else if (file_size > max_size) {
                $(this).next().text(size_err);
                file_input.value = '';
                // document.getElementById('blah').src = '';
                return false;
            }
        }
        return true;
    });
});


