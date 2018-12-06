function OnTextAreaConstruct(arParams) {
    var iInputID   = arParams.oInput.id;
    var iTextAreaID   = iInputID + '_ta';

    var obLabel   = arParams.oCont.appendChild(BX.create('textarea', {
        props : {
            'cols' : 40,
            'rows' : 5,
            'id' : iTextAreaID
        },
        html: arParams.oInput.value
    }));

    $("#"+iTextAreaID).on('keyup', function() {
        $("#"+iInputID).val($(this).val());
    });
}