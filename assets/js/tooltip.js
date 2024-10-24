(function() {
  var svg = document.getElementById('remoteTooltip');
  var tooltipTitle = document.getElementById('tooltip-title');
  var tooltipDesc = document.getElementById('tooltip-desc');
  var triggers = svg.getElementsByClassName('tooltip-trigger');

  for (var i = 0; i < triggers.length; i++) {
    triggers[i].addEventListener('mousemove', showTooltip);
    triggers[i].addEventListener('mouseout', hideTooltip);
  }

  function showTooltip(evt) {
    tooltipTitle.setAttributeNS(null, "class", "visible");
    tooltipTitle.firstChild.data = evt.target.getAttributeNS(null, "data-tooltip-name");
    tooltipDesc.setAttributeNS(null, "class", "card-text visible");
    tooltipDesc.firstChild.data = evt.target.getAttributeNS(null, "data-tooltip-text");
  }

  function hideTooltip(evt) {
    tooltipTitle.setAttributeNS(null, "class", "invisible");
    tooltipDesc.setAttributeNS(null, "class", "card-text invisible");
  }
})()
