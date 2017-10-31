var rowSchedClickHandler = 
    function(row) 
    {
        return function() { 
            window.location.href = 'sched-editor.php?id='+row.getAttribute('data-label');
            };
    };

var rows = document.querySelectorAll('.schedule-row');
for (i=0; i<rows.length; i++) {
    rows[i].onclick = rowSchedClickHandler(rows[i]);
}

var rowDocClickHandler = 
function(row) 
{
    return function() { 
        window.location.href = 'doc-editor.php?action=edit&id='+row.getAttribute('data-label');
        };
};
var docs = document.querySelectorAll('.doc-data-row');
for (i=0; i<docs.length; i++){
    docs[i].onclick = rowDocClickHandler(docs[i]);
}

var rowPostClickHandler = 
function(row) 
{
    return function() { 
        window.location.href = 'post-editor.php?action=edit&id='+row.getAttribute('data-label');
        };
};
var posts = document.querySelectorAll('.post-data-row');
for (i=0; i<posts.length; i++){
    posts[i].onclick = rowPostClickHandler(posts[i]);
}