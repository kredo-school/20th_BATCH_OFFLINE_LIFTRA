const repeatCheck = document.getElementById("repeatCheck");
const repeatTypeArea = document.getElementById("repeatTypeArea");
const repeatType = document.getElementById("repeatType");

const options = document.querySelectorAll(".repeat-option");

repeatCheck.addEventListener("change", () => {

if (repeatCheck.checked) {
    repeatTypeArea.classList.remove("d-none");
} else {
    repeatTypeArea.classList.add("d-none");
    options.forEach(o => o.classList.add("d-none"));
}

});

repeatType.addEventListener("change", () => {

options.forEach(o => o.classList.add("d-none"));

if (repeatType.value === "daily") {
    document.getElementById("dailyArea").classList.remove("d-none");
}

if (repeatType.value === "weekly") {
    document.getElementById("weeklyArea").classList.remove("d-none");
}

if (repeatType.value === "monthly") {
    document.getElementById("monthlyArea").classList.remove("d-none");
}

});