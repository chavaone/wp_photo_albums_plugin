<?php wp_nonce_field( basename( __FILE__ ), 'albums_post_nonce' ); ?>
<div class="upload-block">
<img class="logo" src="http://upload.wikimedia.org/wikipedia/commons/c/ca/Wordpress-logo.png" height="30px">
<p>Sube unha imaxe a web ou usa unha que xa subiches. Este tipo de forma está limitado polo espacio dispoñible no servidor. Así que mellor usar outro metodo cando seña posible.</p>
<input id="gallery_autor" type="text" placeholder="Autor">
<input id="gallery_autor_url" type="text" placeholder="URL Autor">
<select id="gallery_licence">
  <option value="0">all rights reserved</option>
  <option value="1">by-nc-sa</option>
  <option value="2">by-nc-sa</option>
  <option value="3">by-nc-nd</option>
  <option value="4">by</option>
  <option value="5">by-sa</option>
  <option value="6">by-nd</option>
</select>
<button type="button" onclick="app.add_from_gallery ()">Engadir</button>
</div>

<div class="upload-block">
<img class="logo" src="http://upload.wikimedia.org/wikipedia/commons/9/9b/Flickr_logo.png" height="30px">
<p>Pega o ID dun album de Flickr. Para conseguilo collemos os ultimos números dunha URL dun álbum. Por exemplo da url <em>www.flickr.com/photos/sanva/sets/72157636911375153/</em> o ID é <em>72157636911375153</em></p>
<input id="flickr_input" type="text"/>
<button type="button" onclick="app.add_from_flickr ()">Engadir</button>
</div>

<div class="upload-block">
<img src="http://blog.javierh.com/wp-content/uploads/2014/02/URL.jpg" height="30px" class="logo">
<p>Sube unha foto directamente empregando unha url.</p>
<input id="url_titulo" type="text" placeholder="Titulo">
<input id="url_url" type="text" placeholder="URL">
<input id="url_autor" type="text" placeholder="Autor">
<input id="url_url_autor" type="text" placeholder="URL Autor">
<select id="url_licence">
  <option value="0">all rights reserved</option>
  <option value="1">by-nc-sa</option>
  <option value="2">by-nc-sa</option>
  <option value="3">by-nc-nd</option>
  <option value="4">by</option>
  <option value="5">by-sa</option>
  <option value="6">by-nd</option>
</select>
<button type="button" onclick="app.add_from_url ()">Engadir</button>
</div>

<div class="upload-block">
<img class="logo" src="http://www.socialmediaexplorer.com/wp-content/uploads/2013/03/facebook-logo-reversed.png" height="30px">
<p>Pega o ID dun album de Facebook.</p>
<input id="fb_input" type="text"/>
<select id="fb_licence">
  <option value="0">all rights reserved</option>
  <option value="1">by-nc-sa</option>
  <option value="2">by-nc-sa</option>
  <option value="3">by-nc-nd</option>
  <option value="4">by</option>
  <option value="5">by-sa</option>
  <option value="6">by-nd</option>
</select>
<button type="button" onclick="app.add_from_facebook ()">Engadir</button>
</div>

<table class="wp-list-table widefat" id="photos_list">
<thead>
  <tr>
    <th>Foto</th>
    <th>Autor</th>
    <th>Licencia</th>
    <th>Accións</th>
  </tr>
</thead>
<tbody>
</tbody>
<tfoot>
  <tr>
    <th>Foto</th>
    <th>Autor</th>
    <th>Licencia</th>
    <th>Accións</th>
  </tr>
</tfoot>
</table>
<input type="hidden" id="photos_hidden_input" name="photos" value='<?php
$custom = get_post_custom();

if (isset($custom["photos"][0]))
{
  echo $custom["photos"][0];
}
else
{
  echo "[]";
}?>'>
<input type="hidden" id="flickr_api" name="flickr_api_key" value="<?echo get_option('flickr_api_key');?>">
<input type="hidden" id="fb_app_id" name="fb_app_key" value="<?echo get_option('fb_key');?>">
