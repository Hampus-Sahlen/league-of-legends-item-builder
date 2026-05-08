const importDiv = document.querySelector("#importDiv")
const itemStorage = document.querySelector("#itemStorage")
const storageContainer = document.querySelector("#storage-container")
const itemInventory = document.querySelector("#itemInventory")
const inventoryContainer = document.querySelector("#inventory-container")
const itemStats = document.querySelector("#itemStats")
let dragObjects = new Array
let items = new Array

const statNameTranslation = { // translate db names into human readable names
    "health": "Health",
    "health-regen": "Health Regeneration",
    "heal-and-shield-power": "Heal and Shield Power",
    "armor": "Armor",
    "magic-resistance": "Magic Resist",
    "tenacity": "Tenacity",
    "slow-resist": "Slow Resist",
    "attack-speed": "Attack Speed",
    "attack-damage": "Attack Damage",
    "ability-power": "Ability Power",
    "crit-chance": "Crit Chance",
    "crit-damage": "Crit Damage",
    "lethality": "Lethality",
    "magic-pen": "Magic Penetration",
    "life-steal": "Life Steal",
    "omnivamp": "Omnivamp",
    "gold-generation": "Gold generated over 5 seconds",
    "ability-haste": "Ability Haste",
    "mana": "Mana",
    "mana-regen": "Mana Regeneration",
    "movement-speed": "Movement Speed",
    "movement-speed-percent": "Movement Speed",
    "armor-pen-percent": "Armor Penetration",
    "magic-pen-percent": "Magic Penetration"
}

const percentStats = [ // all stats that should have a % behind them
    "slow-resist",
    "heal-and-shield-power",
    "tenacity",
    "attack-speed",
    "omnivamp",
    "life-steal",
    "mana-regen",
    "health-regen",
    "crit-damage",
    "crit-chance",
    "movement-speed-percent", 
    "armor-pen-percent", 
    "magic-pen-percent"
]


document.addEventListener("DOMContentLoaded", e=>{ // setup when page loads
    importItems() // import items
    refreshSlots() // create the slots
    updateStatView()
    
    itemInventory.classList.add("js-itemContainer")
    itemStorage.classList.add("js-itemContainer")

    document.addEventListener("mouseup", stopDraggingObjects)
    document.addEventListener("mousemove", moveDraggedObjects)
})


function importItems() {
    importDiv.remove()

    for (let i = 0; i < importDiv.children.length; i++) {
        const item = importDiv.children[i];
        
        let tempArr = new Object

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
        article.classList.add("item")
        article.dataset["index"] = index
        article.title = item["name"] // hover over shows the name of the item
        article.appendChild(image) 
        // add eventlistener for moving into another zone
        article.addEventListener("mousedown", startDraggingObject)
        // add item to itemstorage
        itemStorage.appendChild(article)
    })
}




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
    this.style.cursor = "grabbing"
    // add button release checker to object
    dragObjects = document.querySelectorAll(".js-dragObject")
}

function moveDraggedObjects(e) { // move all objects that are currently being dragged
    dragObjects.forEach(element => {
        element.style.left = e.clientX - Number(element.dataset.offsetX) + "px"
        element.style.top = e.clientY - Number(element.dataset.offsetY) + "px"
    });
    if (dragObjects.length > 0) {
        if (e.clientY <= 10) {
            window.scroll(window.scrollX, window.scrollY-10)
        }
    }
}

function stopDraggingObjects(e) {
    // stop holding objects when mouse is relesed
    dragObjects.forEach(element => {
        // remove dragging properties
        element.classList.remove("js-dragObject")
        element.style.cursor = ""
        element.style.position = ""
        // assign item to a new container if dropped on one 
        document.querySelectorAll(".js-itemContainer").forEach(container => {
            let rect = container.getBoundingClientRect()
            switch (container) { // may set hitbox to something larger than container
                case itemInventory:
                    rect = inventoryContainer.getBoundingClientRect()
                    break
                case itemStorage:
                    rect = storageContainer.getBoundingClientRect()
                    break
            }
            // creates hitbox for itemContainer, checks if mouse is inside hitbox
            if ((e.clientX >= rect.left && e.clientX <= rect.right) && (e.clientY >= rect.top && e.clientY <= rect.bottom)) {
                // item was dropped on the container
                // console.info("dropped on", container)
                if (container === itemInventory) {
                    let hasSlot = false
                    for (let i = 0; i < itemInventory.children.length; i++) {
                        const slot = itemInventory.children[i];
                        if (slot.classList.contains("slot")) {
                            hasSlot = true
                        }
                    }
                    if (hasSlot) {
                        // console.info("reassigning", element, "to", container)
                        container.appendChild(element);
                        updateStatView() // update the stats displayed
                        refreshSlots()
                    } 
                    // else {
                    //     console.info("itemInventory already has 6 items")
                    // }
                } 
                else {
                // console.info("reassigning", element, "to", container)
                container.appendChild(element);
                updateStatView() // update the stats displayed
                refreshSlots()
                }
            }
        });
    });
    dragObjects = document.querySelectorAll(".js-dragObject") // refresh dragObjects
}

function updateStatView() {
    // console.info("Updating stats")

    // combine stats for displaying
    finishedStats = new Object
    finishedStats.abilities = new Array
    finishedStats.groups = new Array

    for (let i = 0; i < itemInventory.children.length; i++) {
        if (itemInventory.children[i].classList.contains("slot")) {
            continue
        }
        let item = items[itemInventory.children[i].dataset["index"]];
        item = structuredClone(item) // edit item without modifying the stored item
        finishedStats.abilities.push(item["ability"])
        finishedStats.groups.push(item["item-group"]) // use in the future to identify if multiple items from the same group exist
        
        // remove unstackable or undesired attributes
        delete item["ability"]
        delete item["item-group"]
        delete item["index"]
        delete item["image"]
        delete item["name"]
        delete item["ID"]
    
        Object.keys(item).forEach(key=>{
            if (typeof finishedStats[key] !== 'undefined') {
                finishedStats[key] += parseInt(item[key])
            }
            else {
                finishedStats[key] = parseInt(item[key])
            }
        })
    }

    // display stats
    itemStats.innerHTML = ""
    
    if (typeof finishedStats.cost === 'undefined') {
        finishedStats.cost = 0
    }
    p = document.createElement("p")
    p.innerHTML = "Total cost: <span>" + finishedStats.cost + "</span>" // display total cost
    itemStats.appendChild(p)


    let abilityList = [] // temporaraly store abilities to display them at the bottom of the list 
    finishedStats.abilities.forEach(ability=>{ // compile abilities
        if (typeof ability !== 'undefined') {
            p = document.createElement("p")
            p.innerHTML = "Ability: " + ability
            p.classList.add("ability")
            abilityList.push(p)
        }
    })


    delete finishedStats["abilities"]
    delete finishedStats["cost"]
    delete finishedStats["groups"]
    
    Object.keys(finishedStats).sort().forEach(key=>{ // display each stat other than cost and abilities in alphabetical order
        if (finishedStats[key] === 0) return;

        p = document.createElement("p")
        p.innerHTML = statNameTranslation[key]+": "
        span = document.createElement("span") // add the values to a <span> to display them differently
        span.innerHTML = finishedStats[key]
        if (percentStats.includes(key)) { // if stat is a percent stat, add percent to the end
            span.innerHTML += "%"
        }
        p.appendChild(span)
        itemStats.appendChild(p)
    })

    abilityList.forEach(p => { // display abilities
        itemStats.appendChild(p)
    })
}


function refreshSlots() {
    repeatTimes = itemInventory.children.length
    let j = 0
    for (let i = 0; i < repeatTimes; i++) {
        const slot = itemInventory.children[j];

        if (slot.classList.contains("slot")) {
            slot.remove()
        }
        else {
            j++
        }
    }
    
    while (itemInventory.children.length < 6) {
        const slot = document.createElement("div")
        slot.classList.add("slot")
        itemInventory.appendChild(slot)
        // console.log("Added slot")
    }
}