
<table>
    <tbody id="todo_list">
    </tbody>
</table>

<script type="text/javascript">
    var todos = {{ $todos }};
    if(typeof running !== 'undefined'){
        prepData(todos);
    }
</script>
