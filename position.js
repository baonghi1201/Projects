<script type="text/javascript">
countPos=0;
$(document).ready(function () {
    window.console && console.log('Document ready called');
    $('#addPos').click(function (event) {
        event.preventDefault();
        if(countPos>=10){
            alert("Maximum position reached");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
            '<div id="position'+countPos+'">\
                <p> Year:<input type="text" name="year'+countPos+'" value=""/>\
                <input type="button" value="-" \
                onclick="$(\'#position'+countPos+'\').remove(); return false;"></p>\
                <textarea name="desc'+countPos+'" row="8" cols="80"></textarea>\
            </div>');
    });
});