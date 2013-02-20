var data = '';
var index = 0;
$('#nums_1 tr.tr-td-bd').each(function() {
    index++;
    if(index > 100) {
    var tr = $(this);
    var oem = tr.find('td').eq(1).html();
    var num = tr.find('td').eq(27).html();
    data += oem + '#' + num + '@';
    }
})
    $.ajax({
            url: 'http://localhost/4.php',
            data: {"data":data},
            dataType: 'jsonp',
            jsonp: 'jsoncallback',
            beforeSend: function() {},
            complete: function() {},
            success: function(json) {
                    if(json && json.status == 1) {
                            alert(1);
                    } else {
                            alert('œµÕ≥∑±√¶£¨«Î…‘∫Û≥¢ ‘');
                    }
            }
    });

