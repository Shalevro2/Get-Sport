'use strict';
const ourSearch = document.getElementById("inputSearch"); //input text

/*Listener search input bold the li that includes */
ourSearch.addEventListener('input', function (e) {
    let titleList = document.querySelectorAll('.hGroupName');
    let cardList = document.querySelectorAll('.groupCard');
    let rowList = document.querySelectorAll('.rowGroupDiv');

    for (let i = 0; i < cardList.length; i++) {
        if (titleList[i].textContent.includes(e.target.value) && e.target.value.trim()) {
            cardList[i].style.display = "";
        } else {
            cardList[i].style.display = "none";
        }
        if (e.target.value === "") {
            cardList[i].style.display = "";
            if (i < rowList.length)
                rowList[i].style.display = "";
        }
    }
    for (let j = 0; j < cardList.length; j++) {
        if (document.getElementById('rowGroupDiv' + j)) {
            if (!$(`.GroupDiv${j}:visible`).length)
                $('#rowGroupDiv' + j).hide();
        }
    }
}, false);

$('#selectSportCategory').on('change', function () {
    document.forms['formShowGroups'].submit();
});

$('#selectCityCode').on('change', function () {
    document.forms['formShowGroups'].submit();
});