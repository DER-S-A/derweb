/**
 * Componente tabulador
 */

 class HTMLTabsV2 {

    constructor(xId, xtabTitles, xTabContent, xTemplate) {
        this.tabTitles = xtabTitles;
        this.id = xId;
        this.template = xTemplate;
        this.tabContents = xTabContent;
        this.tabs_ids = new Array();
        this.htmlTabContents = new Array();
        this.tabContainer = null;
        this.nav = null;
        this.navTabs = null;
        this.contents = null;
    }

    /**
     * Pongo la funcionalidad en el constructor para que se ejecuta al invocar
     * desde el html a <app-tab></app-tab>
     */
    toHtml() {
        this.__getTabContent();
        this.__getTabTemplate();
        this.__setTabs();
        this.nav.appendChild(this.contents);
        document.getElementById("app-container").appendChild(this.tabContainer);
    }

    /**
     * Obtiene el template del control tab a partir de tabs.html
     */
    __getTabTemplate() {
        this.tabContainer = document.createElement("div");
        this.tabContainer.classList.add("tab-container");
        
        this.nav = document.createElement("nav");
        this.nav.id = this.id + "_nav";
        
        this.navTabs = document.createElement("div");
        this.navTabs.id = this.id +"_list";
        this.navTabs.classList.add("nav");
        this.navTabs.classList.add("nav-tabs");
        this.navTabs.setAttribute("role", "tablist");
        
        this.contents = document.createElement("div");
        this.contents.id = this.id + "_content";
        this.contents.classList.add("tab-content");

        this.nav.appendChild(this.navTabs);
        this.tabContainer.appendChild(this.nav);
    }

    /**
     * Obtiene el contenido de cada tab a partir de los templates seteados en
     * tabContents.
     */
     __getTabContent() {
        this.tabContents.forEach(async (xelement) => {
            getTemplate(xelement, (xresponse) => {
                this.htmlTabContents.push(xresponse);
            });
        });
    }    

    /**
     * Crea las pestañas del control tab a partir de los títulos seteados en
     * tabTitles.
     */
    __setTabs() {
        this.tabTitles.forEach((xelement, xindex) => {
            //var tabList = document.getElementById(this.id + "_list");
            //var tabContent = document.getElementById(this.id + "_content");
            var button = document.createElement("button");
            var id_nav = "nav-" + xelement.replace(/ /g, "-").toLowerCase();
            var ariaSelected = "false";
            var content = document.createElement("div");

            button.classList.add("nav-link");

            if (xindex === 0) {
                button.classList.add("active");
                this.ariaSelected = "true";
            }

            button.id = id_nav + "-tab";
            button.setAttribute("data-bs-toggle", "tab");
            button.setAttribute("data-bs-target", "#" + id_nav);
            button.type = "button";
            button.setAttribute("role", "tab");
            button.setAttribute("aria-controls", id_nav);
            button.setAttribute("aria-selected", ariaSelected);
            button.textContent = xelement;
            this.navTabs.appendChild(button);

            content.classList.add("tab-pane");
            content.classList.add("fade");

            if (xindex === 0) {
                content.classList.add("show");
                content.classList.add("active");
            }

            content.id = id_nav;
            content.setAttribute("role", "tabpanel");
            content.setAttribute("aria-labelledby", button.id);
            this.tabs_ids.push(id_nav);
            content.innerHTML = this.htmlTabContents[xindex];

            this.contents.appendChild(content);
        });    

    }
}
