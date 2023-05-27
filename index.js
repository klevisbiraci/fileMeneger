let addFiles = document.getElementById("create");
let showFiles = document.getElementById("files");
let buttons = document.querySelectorAll("button");
let search = document.getElementById("search");
let del = document.getElementById("del");
let sort = document.getElementById("sort");
let copy = document.getElementById("copy");
let rename = document.getElementById("rename");
let move = document.getElementById("move");
let dirList = document.getElementById("directories");
let popup = document.getElementById("cover");

let dirSelected;

const fileSelected = [];

window.addEventListener("load",async function(e) {
    const res = await fetch("show.php?show=1");
    const data = await res.json();

    if (data.length !== 0) {
        data.forEach(function(element) {
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
            select.addEventListener("click",function(e) {
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

    const resDir = await fetch("show.php?showDir=1");
    const dataDir = await resDir.json();

    if (dataDir.length !== 0) {
        dataDir.forEach(function(element){
            let dir = document.createElement("li");
            let dirName = document.createElement("button");

            dirName.appendChild(document.createTextNode(element[1]));
            dir.appendChild(dirName);
            dirList.appendChild(dir);

        });

        let canceLpopup = document.getElementById("cancel");
        let dirButtons = Array.from(dirList.children);

        dirButtons.forEach(function(element) {
            element.firstChild.addEventListener("mouseover",function(e) {
                this.style.backgroundColor = "rgba(204, 133, 2, 0.479)";
        
            });

            element.firstChild.addEventListener("mouseout",function(e) {
                this.style.backgroundColor = "orange";
                
            });

            element.firstChild.addEventListener("click", async function(e){
                dirSelected = this.textContent;
                popup.style.display = "none";

                if (dirSelected !== null && dirSelected !== undefined) {
                    let violation = 0;
                    fileSelected.forEach(function(file) {
                        if (file === dirSelected) {
                            violation++;

                        }
                    });

                    if (violation !== 0) {
                        alert("you tried to insert a directory inside itself");

                    } else {
                        let formData = new FormData();
                        formData.append("dir",dirSelected);
                        formData.append("files",fileSelected);
            
                        const res = await fetch("move.php",{
                            method: 'POST',
                            body: formData
                        });
                
                        // location.reload();
                        const data = res;
                        console.log(data);
                        console.log(await data.json());
                        
                    }
                }
            });

            canceLpopup.addEventListener("click",function(e){
                popup.style.display = "none";
                    
            });
        });
    }
});

buttons.forEach(function(element) {
    element.addEventListener("mouseover",function(e) {
        this.style.backgroundColor = "rgba(204, 133, 2, 0.479)";

    });

    element.addEventListener("mouseout",function(e) {
        this.style.backgroundColor = "orange";
        
    });
});

addFiles.addEventListener("click",async function(e) {
    let fileName = prompt("File name\n\nNOTE: if the file does not contain \".\",it will create a directory","");
    
    if (fileName !== "" && fileName !== null) {
        let formData = new FormData();
        formData.append("name",fileName);

        if (confirm("are you sure")) {
            const res = await fetch("create.php",{
                method: 'POST',
                body: formData
            });

            const data = await res.json();
            alert(data);
            location.reload();

        }

    } else if (fileName === "") {
        alert("file name cant be empty");

    }
});

del.addEventListener("click",async function(e) {
    if (fileSelected.length === 0) {
        alert("nothing to delete");

    } else {
        if (confirm("are you sure ?")) {
            const res = await fetch("delete.php",{
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(fileSelected)

            });    
            // const data = await res.json();
            // console.log(data);
            location.reload();
        }
    }
});

sort.addEventListener("click",function(e) {
    const sortFiles = Array.from(showFiles.children);
    const fileNameArr = [];
    const liOrdered = [];

    for (let index = 1; index < sortFiles.length; index++) {
        const element = sortFiles[index];
        fileNameArr.push(element.firstChild.textContent);

    }

    fileNameArr.sort(function(a, b) {
        return a.localeCompare(b);

    });

    fileNameArr.forEach(function(eleOrderd) {
        for (let index = 1; index < sortFiles.length; index++) {
            const eleNotOrderd = sortFiles[index];

            if (eleOrderd == eleNotOrderd.firstChild.textContent) {
                liOrdered.push(eleNotOrderd);
                eleNotOrderd.remove();

            }
        }
    });

    liOrdered.forEach(function(orderedEements) {
        showFiles.appendChild(orderedEements);

    });
});

copy.addEventListener("click",async function(e) {
    if (fileSelected.length === 0) {
        alert("nothing to copy");

    } else {
        if(confirm("are you sure ?")) {
            const res = await fetch("copy.php",{
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

rename.addEventListener("click",function(e) {
    if (fileSelected.length === 0) {
        alert("nothing to rename");
        
    } else {
        fileSelected.forEach(async function(element) {
            let newName = prompt("enter a new name");
            let formData = new FormData();
            
            if (newName !== "" && newName !== null) {
                formData.append("new",newName);
                formData.append("old",element);
                const res = await fetch("move.php",{
                    method: 'POST',
                    body: formData
                });
        
                const data = res;
                console.log(data.json());
            }
        });  
        location.reload();
    }
});    

move.addEventListener("click",function() {
    let dirUl = document.getElementById("directories");
    let dirLi = Array.from(dirUl.children);

    if (fileSelected.length === 0) {
        alert("nothing to move");

    } else if (dirLi.length === 0) {
        alert("no directories");
        
    } else {
        popup.style.display = "block";

    }
});

search.addEventListener("keyup",function(e) {
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