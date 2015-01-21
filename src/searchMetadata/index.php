<!DOCTYPE html>
<head>
<title>Kaltura Metadata Search</title>
<link href='http://fonts.googleapis.com/css?family=Open+Sans:700,400,300' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div id="wrapper">
    <div id="search-form">
        <h1>Kaltura Metadata Search</h1>
        <form id="kaltura-metadata-search">
            <label for="search-term">Search Term:</label>
            <input type="search" id="search-term" name="search-term" placeholder="Search term"/>
            
            <label for="select-metadata">Metadata Field:</label>
            <span class="custom-dropdown">
                <select name="select-metadata" id="select-metadata" class="custom-dropdown__select">
                <option value="">Select Metadata Field</option>
                <option value="kaltura-video-name">Video Name</option>
                <option value="kaltura-tags">Tag Name</option>
                <option value="kaltura-category">Category ID</option>
                </select>
            </span>
            
            <input id="submit-button" type="submit" value="Search" />
        
        </form>
        
        <div class="alerts">
            <div class='alert alert-search-term'>
                <p>Please enter a search term</p>
            </div>
            <div class='alert alert-select-metadata'>
                <p>Please select a field</p>
            </div>
        </div>

        <div class="form-response">

        </div>

    </div>

</div>    

<!-- Loading screen -->
<div id="loading">
    <div id="floatingBarsG">
        <div class="blockG" id="rotateG_01">
        </div>
        <div class="blockG" id="rotateG_02">
        </div>
        <div class="blockG" id="rotateG_03">
        </div>
        <div class="blockG" id="rotateG_04">
        </div>
        <div class="blockG" id="rotateG_05">
        </div>
        <div class="blockG" id="rotateG_06">
        </div>
        <div class="blockG" id="rotateG_07">
        </div>
        <div class="blockG" id="rotateG_08">
        </div>
    </div>
</div>
    

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript">

$(document).ready(function(){
// Grab metadata fields and populate form
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: 'getMetadataFields.php',
        success: function (data){
            for (var i = 0; i < data.metadataFields.length; i++){
                var metadataField = data.metadataFields[i];
                $('#select-metadata').append('<option value="' + metadataField + '">' + metadataField + '</option');
            }
        }
    })

});

// Loading screen -- won't pop up unless request takes longer than a second
var timer;

$(document).ajaxStart(function(){
    clearTimeout(timer);
    timer = setTimeout(function(){
        $('#loading').show();
    }, 1000)
})
$(document).ajaxStop(function(){
    clearTimeout(timer);
    $('#loading').hide();
})


// Ajax to post data to formhandler.php
$('#kaltura-metadata-search').submit(function(){
    event.preventDefault();
    $.ajax({
        type: 'POST',
        url: 'formHandler.php',
        data: $(this).serialize(),
        dataType: 'json',
        success: function (data){
            console.log(data);
            // empty response handling
            if (!data.hasSearchTerm){
                $('.alert-search-term').show();
            }
            if (!data.hasSelectMetadata){
                $('.alert-select-metadata').show();
            }
            // result display
            if (data.message){
                $('.form-response').html(data.message);
            }
        }
    });
});
$('#search-term').focus(function(){
    $('.alert-search-term').hide();
});
$('#select-metadata').focus(function(){
    $('.alert-select-metadata').hide();
});
</script>


</body>
</html>
