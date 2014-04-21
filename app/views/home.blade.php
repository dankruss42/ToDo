<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test</title>
</head>
<body>


<h1>Todos:</h1>

<input type='text' id='item_new' class='js_input'/>

<div>
    {{ $listView }}
</div>
<!--TODO: What still needs doing:
1. Make a primary input field to add todo items
2. Add a delete button on todo items
3. prettify -> involve some bootstrap stuffs. HTML is simple
4. Clean up js -> If theres time, switch to BM for front end shizniz
5. Clean up back end. Remove unused code
6. Change database to postgres: just need to get username and password sorted
7. Push changes to repo
8. Get app up somewhere-->

<script type="text/javascript" src="/assets/js/jquery-1.11.0.min.js" ></script>
<script type="text/javascript">

    function prepData(data) {
        //Adds list of elements to HTML and performs event binding

        var list_region = $("#todo_list");

        //Iterate through Todos, and add them to the list
        for(todo in data){
            list_region.append(createRow(todos[todo].id, todos[todo].description));
        }

        //Once all items are there, bind events to them to handle changes
        bindChangeEvents();
        bindClickEvents();
    }

    function createRow(id, description){
        //Commonplace variables for function
        var item_container_tag = "tr";
        var attr_container_tag = "td";

        item = "";
        item += "<"+item_container_tag+">";
        item += "<"+attr_container_tag+" data-id='"+id+"' class='js_todo' id='todo_"+id+"'>";
        item += description;
        item += "</"+attr_container_tag+">";
        item += "</"+item_container_tag+">";

        return item;
    }

    function bindClickEvents(){
        //Clicking on an item
        $(".js_todo").bind('dblclick', function(){
            //Collect relevant data
            $this = $(this);
            value = $this.html();
            id = $this.data('id');

            //Replace container with input field with identifiers
            $this.hide();
            input = createInput(id, value, $this.html());//"<input type='text' name='item_"+id+"' class='js_input' id='input_"+id+"' value='"+value+"' data-old-value='"+$this.html()+"'/>";
            $this.parent().append(input);

            //Set focus to the end of the String in the newly added text field - for UX
            input = $("#input_"+id);
            input.focus();
            input[0].setSelectionRange(value.length, value.length);

            bindChangeEvents();
        });

    }

    function createInput(id, value, oldValue){
        return "<input type='text' name='item_"+id+"' class='js_input' id='input_"+id+"' value='"+value+"' data-old-value='"+oldValue+"'/>";
    }

    function bindChangeEvents(){
        // If enter key is pressed or focus is lost, perform update
        $(".js_input").keyup(function (e) {
            if (e.keyCode == 13) {
                changeListener($(this));
            }
        });

        $('.js_input').bind('blur', function(){
            //Time to update some sheet
            changeListener($(this));
        });
    }

    function changeListener($el){
        //Create, update or delete todo as needed

        $this = $el;
        value = $this.val();

        if($this.attr('id')=="item_new"){
            if(value){

                $.ajax({
                    url:'/todo/new',
                    data: {'description': value },
                    type: 'POST',
                    success: function(response){
                        console.log("response: ");
                        console.log(response);

                        var list_region = $("#todo_list");
                        list_region.append(createRow(response.id, response.description));

                        $this.val('');
                    }
                });

            }
        } else {
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
        }
    }

    //Set flag to true, letting the child view know that prepData function has been declared.
    var running = true;

    //When controller serves this page, before ajaxCalls, it will need to run through prepData to display the Todo items
    prepData(todos);

</script>
</body>
</html>
