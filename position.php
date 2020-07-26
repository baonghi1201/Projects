<!DOCTYPE html>
<html>
<body>
<form method="post">
    <p>
        <lable for=first_name">First Name:</lable>
        <input type="text" name="first_name">
    </p>
    <p>
        <lable for="last_name">Last Name:</lable>
        <input type="text" name="last_name">
    </p>
    <p>
        <lable for="email">Email:</lable>
        <input type="text" name="email">
    </p>
    <p>
        <lable for="headline">Headline:</lable>
        <input type="text" name="headline" size="30">
    </p>
    <p>
        <label for="summary">Summary:</label><br/>
        <textarea name="summary" rows="5" cols="30"></textarea>
    </p>
    <p>
        Position: <input type="submit" id="addPos" value="+">
    <div id="position_fields" >
    </div>
    </p>
    <script>
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
</body>
</html>
