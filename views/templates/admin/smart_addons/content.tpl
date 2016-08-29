<style type="text/css">


.main-wrapper {
    margin: 0 auto;
}

.covering-container .modifing {
    padding-right: 5px;
    padding-left: 5px;
    padding-top: 20px;
    background: #fff;
}

.main-container.background-color {
    margin: 0 auto;
    background:#f1f1f1;
    margin-bottom: 10px;
}
.ft-section {
    padding-bottom: 10px;

}
.star-icon i{
    float:left;
    padding-top: 2px;
    padding-right: 2px;
    font-size:18px;
    color:#FFCC00;}

.text-right i{
    padding-left: 70px;
    padding-top: 2px;
    padding-right: 2px;
}
.covering-container{
    border:1px solid  #dddddd;
}
.use-float { text-align: right; }

.use-float i{
   	display: inline-block;
    text-align: right;
    padding-left: 47px;
    padding-top: 2px;
}

.use-float p{
	display: inline-block;
    text-align: right;
    }
.top-padding{
    padding-top: 10px;
    background:#fafafa;
       border-top:1px solid  #dddddd ;

}
.padding-bottom{
    padding-bottom:20px;
}
.text-wrapper h3{
    color:#00a0d3;
    }
.more-details{
    color:#00a0d3;
    margin-top: 5px;
    display:inline-block;
    padding-left: 5px;
}

.italic{
     font-style: italic;
}
.modifing img{     max-width: 105px;
    padding-left: 11px; }
button {
    border-radius: 2px;
    border: 1px solid #cccccc;
    background: #f7f7f7;
    padding: 3px 10px;
    box-shadow: 0 1px #ccc; 
}
.star-icon i {
    padding-right: 3px;
}
.top-padding{
	padding: 10px;
}
.button-action { text-align: center; }
.text-wrapper h3{ margin-left: 0px !important;padding-left: 0px !important; }
 </style>
<div class="row">
    <form action="#" method="post">
            {if isset($blog_addons) AND $blog_addons}
            {foreach from=$blog_addons item=addon name=blog_addons}
             
            <div class="col-md-6 ">
                    <div class="main-container background-color">
                        <div class="covering-container">
                            <div class="modifing">
                                <div class="row padding-bottom">
                                    <div class="col-md-3">

                                        <img src="{$addon.image_url}" alt="logo">
                                    </div>

                                    <div class="col-md-6">
                                        <div class="text-wrapper">
                                            <h3>{$addon.module_title}</h3>
                                        </div>
                                        <div class="text-wrapper">
                                            <p>{$addon.short_description}</p>
                                        </div>
                                        <div class="text-wrapper">
                                            <span class="italic">{l s='By' mod='smartblog'}<a href="#"> {$addon.author}</a></span>
                                        </div>
                                    </div>

                                    <div class="col-md-3 button-action">
                                      
                                     <a type="butotn" class="btn btn-success" href="http://localhost/prestashop-theme-showcase/prestashop/1.6.1.0/admin123/index.php?controller=AdminSmartAddons&action={$addon.install_status}&addons-name={$addon.module_name}&token={$token}" >
                                            <i class="icon-plus-sign-alt"></i>&nbsp;{$addon.install_status|capitalize} Now
                                        </a>
                                     <a class="more-details" href="">More Details</a>
                                    
                                    
                                    </div>

                                </div>

                                    <div class="row top-padding">
                                        <div class="col-md-4">


                                        <div class="ft-section star-icon">
                                            <i class="icon icon-star" aria-hidden="true"></i>
                                            <i class="icon icon-star" aria-hidden="true"></i>
                                            <i class="icon icon-star" aria-hidden="true"></i>
                                            <i class="icon icon-star" aria-hidden="true"></i>
                                            <i class="icon icon-star-half-o" aria-hidden="true"></i>
                                            <span>(6)</span>
                                        </div>
                                        
                                            
                                        </div>
                                        <div class="col-md-8 ">
                                        <div class="text-right">
                                        <p><strong class="color">Last Updated:</strong>{$addon.last_update}</p>
                                        </div>
                                        <div class="use-float">
                                            <i class="icon icon-check" aria-hidden="true"></i>
                                            <p><strong class="color">Compatible</strong> with your version of Prestashop</p>
                                        </div>
                                        </div>
                                    </div>

                            </div>
                        </div>
                </div>
      
            </div>
            {/foreach}
            {/if}
 </form>
</div>
