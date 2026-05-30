const importDiv = document.querySelector("#importDiv")

const storageContainer = document.querySelector("#storage-container")
const itemStorage = document.querySelector("#itemStorage")

const inventoryContainer = document.querySelector("#inventory-container")
const itemInventory = document.querySelector("#itemInventory")

const itemStats = document.querySelector("#itemStats")

const hoverStatsContainer = document.querySelector("#hover-stats-container")
const hoverStatsTitle = document.querySelector("#hoverStatsTitle")
const hoverStats = document.querySelector("#hoverStats")

const hoverAbilityContainer = document.querySelector("#hover-ability-container")
const hoverAbilityTitle = document.querySelector("#hoverAbilityTitle")
const hoverAbility = document.querySelector("#hoverAbility")

let dragObjects = new Array
let items = new Array
let hoveredItem
let activeGroups = new Array

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
    updateStatView() // add the total cost = 0 to the total stats
    
    itemInventory.classList.add("js-itemContainer")
    itemStorage.classList.add("js-itemContainer")

    document.addEventListener("mouseup", stopDraggingObjects)
    document.addEventListener("mousemove", moveDraggedObjects)

    sortStorage(itemStorage)

})


function importItems() {
    /* there are better ways of doing this https://chatgpt.com/share/6a1b44dd-b194-83eb-9d47-101b3f8b2abd 
       which essentialy is just doing all this in php and passing a json with the completed data
       would have the benefit of easily making the page into html for designers
    */
    importDiv.remove()

    for (let i = 0; i < importDiv.children.length; i++) {
        const item = importDiv.children[i];
        
        let tempObj = new Object
        tempObj["groups"] = new Array
        tempObj["ability"] = new Object
        
        for (let i = 0; i < item.children.length; i++) {
            const stat = item.children[i];

            switch (stat.children[0].innerText) {
                case "__groups__":
                    for (let index = 1; index < stat.children.length; index++) { // start on index 1 to avoid "__groups__"
                        const group = stat.children[index].innerText;
                        tempObj["groups"].push(group)
                    }
                    break;
                
                case "ability":
                    stat.children[1].innerText.split('Unique – ').filter(str => str !== "").forEach(ability => { // unique separates each ability and : separates each ability name from its description
                        let temp = ability.split(': ') // temp = ["ability name", "ability description"]
                        tempObj["ability"][temp[0].trim()] = temp[1].trim()
                    });
                    break;
                    
                    default:
                        tempObj[stat.children[0].innerHTML] = stat.children[1].innerHTML // key = value
                    break;
            }
        }

        items.push(tempObj)
    }

    // sort items in alpabetical order based on item name
    items.sort((a, b) => a["name"].localeCompare(b["name"]))

    // create items
    items.forEach((item, index) => {
        // add image
        const image = document.createElement("img") 
        image.src = "images/" + item["image"]
        image.alt = item["name"]
        // add article container with index value in dataset to find item stats later
        let article = document.createElement("article")
        article.classList.add("item")
        article.dataset["index"] = index
        article.appendChild(image) 
        // add eventlistener for moving into another zone
        article.addEventListener("mousedown", startDraggingObject)
        article.addEventListener("mouseover", showStatsOfItem)
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

    // add placeholder where item used to be
    addPlaceholder(this)

    // make object a dragObject
    this.classList.add("js-dragObject")
    this.style.cursor = "grabbing"
    // add button release checker to object
    dragObjects = document.querySelectorAll(".js-dragObject")
}
function moveDraggedObjects(e) { // move all objects that are currently being dragged
    e.preventDefault()
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
    e.preventDefault()

    // remove placeholders
    removePlaceholders()

    // stop holding objects when mouse is relesed
    dragObjects.forEach(element => {
        // remove dragging properties
        element.classList.remove("js-dragObject")
        element.style.cursor = ""
        element.style.position = ""
        // assign item to a new container if dropped on one 
        document.querySelectorAll(".js-itemContainer").forEach(container => {
            if (container === element.parentNode) return

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
                if (container === itemInventory) { // can be a switch
                    let hasSlot = false
                    for (let i = 0; i < itemInventory.children.length; i++) {
                        const slot = itemInventory.children[i];
                        if (slot.classList.contains("slot")) {
                            hasSlot = true
                        }
                    }
                    if (hasSlot) {
                        // console.info("reassigning", element, "to", container)
                        thisItem = items[parseInt(element.dataset["index"])]
                        let groupDupeCheck = false
                        thisItem.groups.forEach(element => {
                            if (activeGroups.includes(element)) {
                                alert("This item is part of a group that you already have.\nYou can only have one item of each group.")
                                groupDupeCheck = true
                            }
                        });
                        if (groupDupeCheck) return;
                        container.appendChild(element);
                        updateStatView() // update the stats displayed
                        refreshSlots()
                        sortStorage(itemStorage)
                    } 

                } 
                else {
                // console.info("reassigning", element, "to", container)
                container.appendChild(element);
                updateStatView() // update the stats displayed
                refreshSlots()
                sortStorage(itemStorage)
                }
            }
        });
    });
    dragObjects = document.querySelectorAll(".js-dragObject") // refresh dragObjects
}




function updateStatView() {
    // console.info("Updating stats")

    // combine stats for displaying
    const finishedStats = new Object
    finishedStats.abilities = new Object
    finishedStats.groups = new Array

    for (let i = 0; i < itemInventory.children.length; i++) {
        if (itemInventory.children[i].classList.contains("slot")) {
            continue
        }
        let item = items[itemInventory.children[i].dataset["index"]];
        item = structuredClone(item) // edit item without modifying the stored item
        finishedStats.abilities = Object.assign(finishedStats.abilities, item.ability)
        finishedStats.groups = finishedStats.groups.concat(item.groups)

        // remove unstackable or undesired attributes
        const ignoredKeys = new Set([
            "ability",
            "groups",
            "index",
            "image",
            "name",
            "ID"
        ])
    
        Object.keys(item).forEach(key=>{
            if (ignoredKeys.has(key)) return
            if (typeof finishedStats[key] !== 'undefined') {
                finishedStats[key] += parseInt(item[key])
            }
            else {
                finishedStats[key] = parseInt(item[key])
            }
        })
    }


    // display stats
    activeGroups = finishedStats.groups // does not matter that it is a reference
    itemStats.innerHTML = ""
    
    if (typeof finishedStats.cost === 'undefined') {
        finishedStats.cost = 0
    }
    p = document.createElement("p")
    p.innerText = "Total cost:" // display total cost
    span = document.createElement("span")
    span.innerText =  finishedStats.cost
    p.appendChild(span)
    itemStats.appendChild(p)
    delete p
    delete span


    const ignoredKeys = new Set([
        "abilities", 
        "cost", 
        "groups"
    ])

    Object.keys(finishedStats).sort().forEach(key=>{ // display each stat other than cost and abilities in alphabetical order
        if (finishedStats[key] === 0 || ignoredKeys.has(key)) return;

        const p = document.createElement("p")
        p.innerText = statNameTranslation[key]+": "
        const span = document.createElement("span") // add the values to a <span> to display them differently
        span.innerText = finishedStats[key]
        if (percentStats.includes(key)) { // if stat is a percent stat, add percent to the end
            span.innerText += "%"
        }
        p.appendChild(span)
        itemStats.appendChild(p)
    })

    if (finishedStats.groups.length > 0) {
        const p = document.createElement("p")
        p.innerText = "Item Groups:"
        const span = document.createElement("span") // add the values to a <span> to display them differently
        let spanText = ""
        finishedStats.groups.forEach(element => {
            spanText += element + "\n"
        });
        span.innerText = spanText.trim()
        p.appendChild(span)
        itemStats.appendChild(p)
    }

    for (const [title, description] of Object.entries(finishedStats.abilities)) { // reads the properties of finishedStats.abilities (aka the ability title and ability description)
        const h3 = document.createElement("h3")
        h3.innerText = title
        h3.dataset.description = description
        h3.addEventListener("mouseover", showAbilityDescription)
        itemStats.appendChild(h3)
    }
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


function showStatsOfItem(e) {
    // if (this.classList.contains("js-dragObject")) return // do not show stats on a dragged object
    if (dragObjects.length) return // do not show stats if any object is being dragged

    hoveredItem = this // used in moveItemStatsDisplay
    document.addEventListener("mousemove", moveItemStatsDisplay) // move stat display with the mouse, and remove it if mouse isnt on stat display or this item
    // position info box
    hoverStatsContainer.style.display = "block"
    moveItemStatsDisplay(e)

    // display stats
    let item = structuredClone(items[this.dataset["index"]])
    
    hoverStatsTitle.innerText = item["name"]

    hoverStats.innerHTML = ""

    if (typeof item.cost === 'undefined') {
        item.cost = 0
    }
    p = document.createElement("p")
    p.innerText = "Cost:" // display total cost
    span = document.createElement("span")
    span.innerText =  item.cost
    p.appendChild(span)
    hoverStats.appendChild(p)
    delete p
    delete span
    
    const ignoredKeys = new Set([
        "ability",
        "cost",
        "groups",
        "ID",
        "image",
        "name"
    ])
    
    Object.keys(item).sort().forEach(key=>{ // display each stat other than cost and abilities in alphabetical order
        if (item[key] === 0 || ignoredKeys.has(key)) return;

        const p = document.createElement("p")
        p.innerText = statNameTranslation[key]+": "
        const span = document.createElement("span") // add the values to a <span> to display them differently
        span.innerText = item[key]
        if (percentStats.includes(key)) { // if stat is a percent stat, add percent to the end
            span.innerText += "%"
        }
        p.appendChild(span)
        hoverStats.appendChild(p)
    })

    if (item.groups.length > 0) {
        const p = document.createElement("p")
        p.innerText = "Item Groups:"
        const span = document.createElement("span") // add the values to a <span> to display them differently
        let spanText = ""
        item.groups.forEach(element => {
            spanText += element + "\n"
        });
        span.innerText = spanText.trim()
        p.appendChild(span)
        hoverStats.appendChild(p)
    }

    for (const [title, description] of Object.entries(item.ability)) { // reads the properties of item.ability (aka the ability title and ability description)
        const p = document.createElement("p")
        p.innerText = title + ": " + description 
        p.classList.add("ability")
        hoverStats.appendChild(p)
    }
}
function moveItemStatsDisplay(e) {
    if (hoveredItem.classList.contains("js-dragObject")) { // stop showing stats if object is being dragged
        hoverStatsContainer.style.display = ""
        document.removeEventListener("mousemove", moveItemStatsDisplay)
        return
    }

    const rect = hoveredItem.getBoundingClientRect()
    if ((e.clientX >= rect.left && e.clientX <= rect.right) && (e.clientY >= rect.top && e.clientY <= rect.bottom)) { // if cursor is above the item
        let offsetY = 0
        const statBoxRect = hoverStatsContainer.getBoundingClientRect()
        if (e.clientY + statBoxRect.height > window.innerHeight) { // if bottom of the box is below the bottom of the window
            offsetY = e.clientY + statBoxRect.height - window.innerHeight // returns the amount of pixels the bottom of the box would be below the bottom of the window
        }

        hoverStatsContainer.style.left = (e.clientX + window.scrollX + 5) + "px"
        hoverStatsContainer.style.top = (e.clientY + window.scrollY - offsetY) + "px"
    }
    else {
        hoverStatsContainer.style.display = ""
        document.removeEventListener("mousemove", moveItemStatsDisplay)
    }
}

function showAbilityDescription(e) {
    hoverAbility.innerHTML = ""
    hoveredText = this
    document.addEventListener("mousemove", moveAbilityDescription) // move ability display with the mouse, and remove it if mouse isnt over the text anymore
    // position info box
    hoverAbilityContainer.style.display = "block"
    moveAbilityDescription(e)

    hoverAbilityTitle.innerText = this.innerText

    const p = document.createElement("p")
    p.classList.add("ability")
    p.innerText = this.dataset.description
    hoverAbility.appendChild(p)
}
function moveAbilityDescription(e) {
    const rect = hoveredText.getBoundingClientRect()
    if ((e.clientX >= rect.left && e.clientX <= rect.right) && (e.clientY >= rect.top && e.clientY <= rect.bottom)) { // if cursor is above the item
        let offsetX = 0
        let offsetY = 0
        const statBoxRect = hoverAbilityContainer.getBoundingClientRect()
        if (e.clientY + statBoxRect.height > window.innerHeight) { // if bottom of the box is below the bottom of the window
            offsetY = e.clientY + statBoxRect.height - window.innerHeight // returns the amount of pixels the bottom of the box would be below the bottom of the window
        }
        if (e.clientX + statBoxRect.width > window.innerWidth) { // if right of the box is outside the right of the window
            offsetX = 5 + e.clientX + statBoxRect.width - window.innerWidth // returns the amount of pixels the right of the box would be outside the right of the window
        }

        hoverAbilityContainer.style.left = (e.clientX + window.scrollX + 5 - offsetX) + "px"
        hoverAbilityContainer.style.top = (e.clientY + window.scrollY - offsetY) + "px"
    }
    else {
        hoverAbilityContainer.style.display = ""
        document.removeEventListener("mousemove", moveAbilityDescription)
    }
}

function addPlaceholder(element) { 
    /* makes a placeholder div with dimensions identical to element, inserting it before element 
    expects element to be removed
    placeholder has class "js-placeholder" and can be removed by selecting the class
    */
    const rect = element.getBoundingClientRect()

    let placeholder = document.createElement("div")
    placeholder.classList.add("js-placeholder")

    switch (element.parentNode) {
        case itemInventory:
            placeholder.classList.add("slot")
            break
        default:
            placeholder.style.height = rect.height + "px"
            placeholder.style.width = rect.width + "px"
    }

    element.parentNode.insertBefore(placeholder, element)
}
function removePlaceholders() {
    document.querySelectorAll(".js-placeholder").forEach(placeholder => {
        placeholder.remove()
    })
}


function sortStorage(parent) {
    const sortArray = []
    for (let i = 0; i < parent.children.length; i++) {
        const element = parent.children[i];
        sortArray.push(element)
    }

    sortArray.forEach(element => element.remove())

    sortArray.sort((a, b) => parseInt(a.dataset["index"]) - parseInt(b.dataset["index"]))

    sortArray.forEach(element => parent.appendChild(element))
}
