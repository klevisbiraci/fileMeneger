let addFiles = document.getElementById("create");
let showFiles = document.getElementById("files");
let buttons = document.querySelectorAll("button");
let search = document.getElementById("search");
let del = document.getElementById("del");
const fileSelected = [];

window.addEventListener("load",async function(e){
    const res = await fetch("show.php?show=1");
    const data = await res.json();
    if (data.length !== 0) {
        data.forEach(function(element){
            let lis = document.createElement("li");
            let select = document.createElement("input");
            select.type = "radio";
            for (let index = 1; index < element.length; index++) {
                const elementToShow = element[index];
                let par = document.createElement("p");
                par.appendChild(document.createTextNode(elementToShow));
                lis.appendChild(par);
            }
            showFiles.appendChild(lis);
            lis.appendChild(select);
            let checkClick = 0;
            select.addEventListener("click", function(e){
                let thisLi = this.parentElement;
                let ArrThisLi = Array.from(thisLi.children);
                checkClick++;
                if (checkClick > 1) {
                    this.checked = false;
                    let index = fileSelected.indexOf(ArrThisLi[0].textContent);
                    if (index !== -1) {
                        fileSelected.splice(index,1);
                    }
                    checkClick = 0;
                } else {
                    fileSelected.push(ArrThisLi[0].textContent);
                }
            });
        });
    }
});

buttons.forEach(function(element){
    element.addEventListener("mouseover",function(e){
        e.target.style.backgroundColor = "rgba(204, 133, 2, 0.479)";
    });

    element.addEventListener("mouseout",function(e){
        e.target.style.backgroundColor = "orange";
    });
});

addFiles.addEventListener("click",async function(e){
    let fileName = prompt("file name\nNOTE: if the file does not contain \".\",it will create a directory","");
    if(fileName !== "" || fileName !== null){
        let formData = new FormData();
        formData.append("name",fileName);

        if(confirm("are you sure")){
            const res = await fetch("create.php",{
                method: 'POST',
                body: formData}
                );
            const data = res.json();
            location.reload();
        }
    }else{
        alert("file name cant be empty");
    }
});

del.addEventListener("click",async function(e){
    if(fileSelected.length === 0){
        alert("nothing to delete");
    }else {
        if(confirm("are you sure ?")){
            const res = await fetch("delete.php",{
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(fileSelected)
            });    
            location.reload();
        }
    }
});

search.addEventListener("keyup", function(e){
    let sBarToLow = e.target.value.toLowerCase();
    const li = Array.from(showFiles.children); 
    for (let index = 1; index < li.length; index++) {
        let nameToSearch = li[index].firstChild.textContent;
        if (nameToSearch.toLowerCase().indexOf(sBarToLow) != -1) {
            li[index].style.display = "flex";
        } else {
            li[index].style.display = "none";
        }
    }
});