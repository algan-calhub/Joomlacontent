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
    var direct=document.getElementById('direct-input');
    if(direct){
        var form=document.getElementById('direct-form');
        var error=document.getElementById('direct-error');
        var counter=document.getElementById('direct-counter');
        function updateCounter(){
            var pos=direct.selectionStart;
            var lines=direct.value.substr(0,pos).split('\n');
            var line=lines.length;
            var col=lines[lines.length-1].length+1;
            counter.textContent=line+':'+col;
        }
        direct.addEventListener('input',updateCounter);
        direct.addEventListener('click',updateCounter);
        direct.addEventListener('keyup',updateCounter);
        direct.addEventListener('keydown',function(e){
            if(e.key==='Enter'&&e.ctrlKey){form.requestSubmit();}
        });
        direct.addEventListener('dragover',function(e){e.preventDefault();direct.classList.add('hover');});
        direct.addEventListener('dragleave',function(){direct.classList.remove('hover');});
        direct.addEventListener('drop',function(e){
            e.preventDefault();
            var file=e.dataTransfer.files[0];
            if(file){
                var reader=new FileReader();
                reader.onload=function(ev){direct.value=ev.target.result;updateCounter();};
                reader.readAsText(file);
            }
            direct.classList.remove('hover');
        });
        if(form){
            form.addEventListener('submit',function(e){
                error.style.display='none';
                var val=direct.value.trim();
                if(!val){
                    e.preventDefault();
                    error.style.display='block';
                    return;
                }
                var valid=true;
                try{JSON.parse(val);}
                catch(err){
                    if(val.indexOf(',')>-1){
                        var rows=val.split('\n');
                        if(rows.length<2){valid=false;}
                    }else if(val.split('\n').length<2){
                        valid=false;
                    }
                }
                if(!valid){
                    e.preventDefault();
                    error.style.display='block';
                }
            });
        }
        updateCounter();
    }
});
