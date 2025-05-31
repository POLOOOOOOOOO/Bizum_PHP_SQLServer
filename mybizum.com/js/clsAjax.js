class clsAjax {

    constructor(url, app) {
        this.xhttp = new XMLHttpRequest();
        this.event = new Event('clsAjax-onLoad'); //Add document.addEventListener('clsAjax-onLoad', this.MYMETHOD.bind(this), false); to the main class for this to work.
        this.app = app;
        this.url = url;
        this.xhttp.addEventListener("load", this._onLoad.bind(this), false); 
        this.xhttp.addEventListener("error", this._onError.bind(this), false);
        this.xml = null;
       // this.Execute();
        
    }
/////////////////////////////////////////////////////////////////

    Call(){
        this.xhttp.open('GET', this.url, true);
        this.xhttp.send(null);
    }

/////////////////////////////////////////////////////////////////

    _onLoad() {
        
        if (this.xhttp.readyState === this.xhttp.DONE) {
            if (this.xhttp.status === 200) {
                this.xml = this.xhttp.responseText;
                this._dispatchEvent();
            }
        }
    };

/////////////////////////////////////////////////////////////////

    _dispatchEvent(){ 
        console.log('CALL_RETURNED  == ');// + e.keyCode);
      var new_event= new CustomEvent("CALL_RETURNED")
      document.dispatchEvent(new_event);


    }

/////////////////////////////////////////////////////////////////

/////////////////////////////////////////////////////////////////   


    _onError(){
        console.log("Document not loaded");
    }        


}