document.addEventListener('DOMContentLoaded',function(){
    var drop=document.getElementById('drop-zone');
    if(drop){
        var input=drop.querySelector('input[type=file]');
        drop.addEventListener('dragover',function(e){e.preventDefault();drop.classList.add('hover');});
        drop.addEventListener('dragleave',function(){drop.classList.remove('hover');});
        drop.addEventListener('drop',function(e){e.preventDefault();input.files=e.dataTransfer.files;drop.classList.remove('hover');});
    }
    var prompt=document.getElementById('gpt-prompt');
    var copy=document.getElementById('copy-prompt');
    if(copy){
        copy.addEventListener('click',function(){navigator.clipboard.writeText(prompt.value);});
    }
});
