var tableSection = document.querySelectorAll('section')[1];
var form  		 = document.querySelector('form');
var jsBtn 		 = document.querySelectorAll('button')[0];
var phpBtn 		 = document.querySelectorAll('button')[1];
var phpTable     = document.querySelector('#php-table');

console.log(phpTable);

// Generate table w/ default values
document.body.onload = generate();

// Submit form if btn is clicked
jsBtn.addEventListener('click', generate);

// Generate table
function generate(e){
	
	// Get input values from user
	var baseUrl   = document.querySelector('#base_url').value;
	var id 		  = document.querySelector('#increment_id').value;
	var art   	  = document.querySelector('#art').value;
	var teamNames = document.querySelector('#teams').value.toUpperCase();
	
	// Create table
	var table = document.createElement('table');
	
	// Add id name
	table.id = 'js-table';
	
	// Create tr element
	var tr = document.createElement('tr');
	
	// Create th elements
	var th = document.createElement('th');
	th.appendChild(document.createTextNode('No'));
	tr.appendChild(th);
	
	var th = document.createElement('th');
	th.appendChild(document.createTextNode('Team Name'));
	tr.appendChild(th);
	
	for(var i = 1; i <= 6; i++){
		
		// Increment id
		var increment = id + '-' + [i];
		
		// Create th element
		th = document.createElement('th');
		th.appendChild(document.createTextNode(increment));
		tr.appendChild(th);
		table.appendChild(tr);
	}
	
	// Create th element
	var th = document.createElement('th');
	th.appendChild(document.createTextNode(id + '-IP'));
	tr.appendChild(th);
	
	// Loop through teamNames
	var teamNames = teamNames.split(',');
	for(var i = 0; i < teamNames.length; i++){
			
		var teamName = teamNames[i];
		
		// Create tr
		var tr = document.createElement('tr');

		// No Col
		var td = document.createElement('td');
		td.appendChild(document.createTextNode(i + 1));
		tr.appendChild(td);

		// Team Name Col
		var td = document.createElement('td');
		td.appendChild(document.createTextNode(teamName));
		tr.appendChild(td);

		// create td w/ links
		for(var k = 1; k <= 6; k++){
			
			var inc = id + '-' + [k];
			var td = document.createElement('td');
			td.appendChild(document.createTextNode(inc));
			
			var a = baseUrl + '?id=' + inc + '_' + teamName;
			
			td.innerHTML = '<a href="' + a + '" target="_blank" title="' + a + '">' + inc + '</a>';
			
			tr.appendChild(td);
		}
		
		// IP Col
		var td = document.createElement('td');
		td.appendChild(document.createTextNode(id + '-IP'));
		tr.appendChild(td);
		
		var a = baseUrl + '?id=' + id + '-IP_' + teamName;
		td.innerHTML = '<a href="' + a + '" target="_blank" title="' + a + '">' + id + '-IP</a>';
		
		table.appendChild(tr);
		tableSection.appendChild(table);		
	}
	
	// Remove current table when new table is generated via JS
	table.previousSibling.remove(table);

}

// Remove JS table is php table is generated
if(phpTable){
	var jsTable = document.querySelector('#js-table');
	jsTable.parentNode.removeChild(jsTable);
}