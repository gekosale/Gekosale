 
 
{
  "bg_colour": "#ffffff",
 
  "elements":[
      {
      "type":      "line",
      "width": 1,
     
      "dot-style": {
				"type":"solid-dot", "colour":"#000000", "dot-size": 4,
				"tip":"{{ date }}-#x_label#<br>Nowych klientów:#val#" },
      "text": "",
      "colour": "#000000",
      "values" : {{ elements }}
      }
      
  ],
 
  "x_axis":{
    "stroke": 2,
    "rotate": "vertical",
    "font-size": 7,
    "tick-height": 5,
    "colour":"#000000",
    "grid-colour":"#e6e5e5",
    "labels": {
   
     "labels":    
     {{ x_axis }}
     }
   },
 
  "y_axis":{
    "stroke":      2,
    "tick-length": 5,
    "colour":      "#000000",
    "grid-colour": "#e6e5e5",
    "offset":      0,
    "max":         " {{ max }}",
    "steps": 1000  }
  ,
  "tooltip":{
    "shadow":false,
    "stroke":1,
    "colour":"#b62183",
    "background":"#ffffff",
    "title":"{font-size: 14px; color: #000000;}",
    "body":"{font-size: 10px; font-weight: bold; color: #000000;}"
  }
}
