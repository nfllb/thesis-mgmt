var hamburger = document.querySelector(".hamburger");
hamburger.addEventListener("click", function(){
    document.querySelector("body").classList.toggle("active");
});

// displayedColumns: string[] = ['name', 'weight', 'symbol', 'position'];
//     columnsToDisplay: string[] = this.displayedColumns.slice();
//     data: PeriodicElement[] = ELEMENT_DATA;

function copyTable() {
    console.log('copyTable');
    // let nodeData = this.data;
    // var tbl = document.createElement('table');
    // var tblBody = document.createElement('tbody');
    // var headerow = document.createElement('tr');
    // var header = this.displayedColumns;
    // headerow.innerHTML = `<td>${header[0]}</td><td>${header[1]}</td><td>${header[2]}</td><td>${header[3]}</td>`;
    // tblBody.appendChild(headerow);
    // nodeData.forEach((data) => {
    //     var row = document.createElement('tr');
    //     row.innerHTML = `<td>${data.name}</td><td>${data.weight}</td><td>${data.symbol}</td><td>${data.position}</td>`;
    //     tblBody.appendChild(row);
    // });
    // tbl.appendChild(tblBody);
    // document.body.appendChild(tbl);
    // // Copy the table element innerText to clipboard
    // navigator.clipboard.writeText(tbl.innerText);
    // // Hide the table element from DOM after copied
    // tbl.style.display = "none";
}