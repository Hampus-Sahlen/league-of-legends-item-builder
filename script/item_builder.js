const importDiv = document.querySelector("#importDiv")
const itemStorage = document.querySelector("#itemStorage")
const itemInventory = document.querySelector("#itemInventory")
const itemStats = document.querySelector("#itemStats")
let dragObjects = new Array


importDiv.remove()



let items = new Array

for (let i = 0; i < importDiv.children.length; i++) {
    const item = importDiv.children[i];
    
    let tempArr = new Array

    for (let i = 0; i < item.children.length; i++) {
        const stat = item.children[i];
        tempArr[stat.children[0].innerHTML] = stat.children[1].innerHTML // key = value
    }

    items.push(tempArr)
}

items.forEach((item, index) => {
    // add image
    let image = document.createElement("img") 
    image.src = "images/" + item["image"]
    image.alt = item["name"]
    // add article container with index value in dataset to find item stats later
    let article = document.createElement("article")
    article.dataset["index"] = index
    article.title = item["name"] // hover over shows the name of the item
    article.appendChild(image) 
    // add eventlistener for moving into another zone
    article.addEventListener("mousedown", startDraggingObject)
    // add item to itemstorage
    itemStorage.appendChild(article)
})



function startDraggingObject(e) { // start dragging an object
    e.preventDefault()
    const rect = this.getBoundingClientRect()
    // calculate offset for where the mouse grabbed the object
    this.dataset.offsetX = e.clientX - rect.left
    this.dataset.offsetY = e.clientY - rect.top
    this.style.left = e.clientX - Number(this.dataset.offsetX) + "px"
    this.style.top = e.clientY - Number(this.dataset.offsetY) + "px"
    this.style.bottom = ""
    this.style.right = ""
    this.style.position = "fixed"
    // make object a dragObject
    this.classList.add("js-dragObject")
    // add button release checker to object
    dragObjects = document.querySelectorAll(".js-dragObject")
}

document.addEventListener("mousemove", moveDraggedObjects)
function moveDraggedObjects(e) { // move all objects that are currently being dragged
    dragObjects.forEach(element => {
        element.style.left = e.clientX - Number(element.dataset.offsetX) + "px"
        element.style.top = e.clientY - Number(element.dataset.offsetY) + "px"
    });
}

itemInventory.classList.add("js-itemContainer")
itemStorage.classList.add("js-itemContainer")
document.addEventListener("mouseup", stopDraggingObjects)
function stopDraggingObjects(e) {
    // stop holding objects when mouse is relesed
    dragObjects.forEach(element => {
        // remove dragging properties
        element.classList.remove("js-dragObject")
        element.style.position = ""
        // assign item to a new container if dropped on one 
        document.querySelectorAll(".js-itemContainer").forEach(container => {
            const rect = container.getBoundingClientRect();
            // creates hitbox for itemContainer, checks if mouse is inside hitbox
            if ((e.clientX >= rect.left && e.clientX <= rect.right) && (e.clientY >= rect.top && e.clientY <= rect.bottom)) {
                // item was dropped on the container
                console.info("reassigning", element, "to", container)
                container.appendChild(element);
            }
        });
    });
    dragObjects = document.querySelectorAll(".js-dragObject")
}