document.addEventListener('DOMContentLoaded',function(){
    var drop=document.getElementById('drop-zone'),input;
    if(drop){
        input=drop.querySelector('input[type=file]');
        drop.addEventListener('dragover',function(e){e.preventDefault();drop.classList.add('hover');});
        drop.addEventListener('dragleave',function(){drop.classList.remove('hover');});
        drop.addEventListener('drop',function(e){e.preventDefault();input.files=e.dataTransfer.files;drop.classList.remove('hover');});
    }
    var prompt=document.getElementById('gpt-prompt');
    var copy=document.getElementById('copy-prompt');
    if(copy){
        copy.addEventListener('click',function(){navigator.clipboard.writeText(prompt.value);});
    }
    var send=document.getElementById('gpt-send');
    if(send){
        send.addEventListener('click',function(){
            var text=document.getElementById('gpt-input').value;
            fetch('index.php?option=com_contentimporter&task=import.chatgpt&format=raw',{
                method:'POST',
                body:new URLSearchParams({prompt:text})
            }).then(function(r){return r.text();}).then(function(res){
                if(input){
                    var blob=new Blob([res],{type:'application/json'});
                    var file=new File([blob],'chatgpt.json',{type:'application/json'});
                    var dt=new DataTransfer();
                    dt.items.add(file);
                    input.files=dt.files;
                }
            });
        });
    }
});
