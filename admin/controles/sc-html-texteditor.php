<?php 

	/**
	 * Control editor de texto html
	 */
	class HtmlRichText
	{
		var $id = "";
		var $value = "";

		function __construct($xid, $xvalue)
		{
			$this->id = $xid;
			$this->value = $xvalue;
		}

		function setValue($xvalue)
		{
			$this->value = $xvalue;
		}

		function getvalue()
		{
			return $this->value;
		}

		function getId()
		{
			return $this->id;
		}

		function toHtml()
		{
			$res = "\n<!-- HtmlRichText -->\n";

			// TODO: el size crea formato propio, ej <span class="ql-size-large"> [{ 'size': []}],

			$res .= "<div id=\"" . $this->getId() . "_editor\">
						" . $this->getvalue() . "
					</div>
				   <script>
				   Quill.register(\"modules/htmlEditButton\", htmlEditButton);
				   var toolbarOptions = [
						['bold', 'italic', 'underline', 'strike'],
						[{ 'font': [] }],
						[{ 'direction': 'rtl' }],     
						[{ 'color': [] }, { 'background': [] }],
						[{ 'align': [] }],
						['link', 'image'],
						['clean']
					];
						var quill = new Quill('#" . $this->getId() . "_editor', {
							theme: 'snow',
							modules: {
								toolbar: {
								  container: toolbarOptions
								},  
								htmlEditButton: { debug: false, syntax: true }
							}
						});
						
					function updateQuillValue()
					{
						qhtml = quill.root.innerHTML.trim();
						document.getElementById(\"" . $this->getId() . "\").value = qhtml;
					}
					
					</script>
					<input type=\"hidden\" name=\"" . $this->getId() . "\"  id=\"" . $this->getId() . "\" value=\"\" />"; 

			return $res;
		}
	}
	
?>