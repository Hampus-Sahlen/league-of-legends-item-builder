const importDiv = document.querySelector("#importDiv")
const itemStorage = document.querySelector("#itemStorage")
const itemInventory = document.querySelector("#itemInventory")
const itemStats = document.querySelector("#itemStats")

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

items.forEach(item => {
    let image = document.createElement("img")
    image.src = "images/" + item["image"]
    image.alt = item["name"]
    let article = document.createElement("article")
    article.appendChild(image)
    itemStorage.appendChild(article)
})