var StableAPI = (function () {
				
	var getCookieByName = function(name) {
		const match = document.cookie.match(new RegExp("(^| )" + name + "=([^;]+)"));
 
		if (match) return match[2];
		
		return null;
	}
	
	var loadStableSDK = function() {
		var info = 'MCP URL: ' + decodeURIComponent(getCookieByName('stable_mcp_url')) + "\r\n" + 'Chat ID: ' + getCookieByName('stable_chat_id');
			
		bridle_script = document.createElement('script');
		bridle_script.src = 'https://bridle.cleanslice.org/sdk/latest.js';
		bridle_script.setAttribute('data-api-url', 'https://api.ranch.cleanslice.org');
		bridle_script.setAttribute('data-agent-id', getCookieByName('stable_agent_id'));
		bridle_script.setAttribute('data-token', getCookieByName('stable_token'));
		bridle_script.setAttribute('data-prompt', info);
		bridle_script.setAttribute('data-mode', 'floating');
		bridle_script.async = false;
					
		document.querySelector('body').appendChild(bridle_script);
	};	
	
	var init = function() {
		loadStableSDK();
	};
	
	return {
		init: init
	};
}());

window.addEventListener('load', function () {
	StableAPI.init();
});