<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test</title>
</head>
<body>


<h1>Todos:</h1>
<div>
    {{ $listView }}
</div>


<script type="text/javascript" src="/assets/js/jquery-1.11.0.min.js" ></script>
<script type="text/javascript">

    function prepData(data) {
        //Adds list of elements to HTML and performs event binding

        //Commonplace variables for function
        var list_region = $("#todo_list");
        var item_container_tag = "tr";
        var attr_container_tag = "td";

        //Iterate through Todos, and add them to the list
        for(todo in data){
            item = "";
            item += "<"+item_container_tag+">";
            item += "<"+attr_container_tag+" data-id='"+todos[todo].id+"' class='js_todo' id='todo_"+todos[todo].id+"'>";
            item += todos[todo].description;

            item += "</"+attr_container_tag+">";
            item += "</"+item_container_tag+">";

            list_region.append(item);
        }
        //Once all items are there, bind events to them to handle changes
        bindEvents();
    }

    function bindEvents(){
        //Clicking on an item
        $(".js_todo").bind('dblclick', function(){
            //Collect relevant data
            $this = $(this);
            value = $this.html();
            id = $this.data('id');

            //Replace container with input field with identifiers
            $this.hide();
            input = "<input type='text' name='item_"+id+"' class='js_input' id='input_"+id+"' value='"+value+"' data-old-value='"+$this.html()+"'/>";
            $this.parent().append(input);

            //Set focus to the end of the String in the newly added text field - for UX
            input = $("#input_"+id);
            input.focus();
            input[0].setSelectionRange(value.length, value.length);


            $('.js_input').bind('blur', function(){
                //Time to update some sheet
                $this = $(this);
                value = $this.val();
                id = $this.attr('id').replace('input_','');
                old_value = $this.data('old-value');


                if(value !== old_value){
                    if(value == ""){
                        //Item has been changed to blank -> Delete from db and dom
                        $.ajax({
                            url: '/todo/'+id,
                            type: 'DELETE',
                            success: function(response) {
                                if(response.status==200){
                                    //Remove the whole tr element
                                    $this.parent().remove();
                                } else if(response.status == 400 ) {
                                    console.log("Error: " + response.message);
                                }
                            }
                        });
                    } else {
                        //Item has been updated, update in db and dom
                        $.ajax({
                            url: '/todo',
                            data: {'id': id, 'description': value },
                            type: 'POST',
                            success: function(response) {
                                if(response.status==200){
                                    //Remove input field, show updated item
                                    $this.remove();
                                    $("#todo_"+id).html(response.description);
                                    $("#todo_"+id).show();
                                } else if(response.status == 400 ) {
                                    console.log("Error: " + response.message);
                                }
                            }
                        });

                    }
                } else {
                    //If nothing was changed, just remove the input field and replace with text
                    $this.remove();
                    $("#todo_"+id).show();
                }
            });
        });

    }

    //Set flag to true, letting the child view know that prepData function has been declared.
    var running = true;

    //When controller serves this page, before ajaxCalls, it will need to run through prepData to display the Todo items
    prepData(todos);

</script>
</body>
</html>
