

app = {};

app.init_fb_api = function ()
{

  var fb_appId = $("input#fb_app_id").val ();

  window.fbAsyncInit = function() {
    FB.init({
      appId      : fb_appId,
      xfbml      : true,
      version    : 'v2.0',
      channelUrl : '/channel.html'
    });
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "http://connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));
};


app.flickr_key = function ()
{
  return $("input#flickr_api").val ();
};


app.licences_images = [
  "",
  "http://mirrors.creativecommons.org/presskit/buttons/80x15/png/by-nc-sa.png",
  "http://mirrors.creativecommons.org/presskit/buttons/80x15/png/by-nc-sa.png",
  "http://mirrors.creativecommons.org/presskit/buttons/80x15/png/by-nc-nd.png",
  "http://mirrors.creativecommons.org/presskit/buttons/80x15/png/by.png",
  "http://mirrors.creativecommons.org/presskit/buttons/80x15/png/by-sa.png",
  "http://mirrors.creativecommons.org/presskit/buttons/80x15/png/by-nd.png"
];

app.add_from_flickr = function ()
{
  var photosetid =  $("#flickr_input").val ();

  $.ajax ({
    type: "GET",
    url: "https://api.flickr.com/services/rest/",
    data: {
      method : "flickr.photosets.getPhotos",
      api_key: app.flickr_key (),
      photoset_id: photosetid,
      format: "json"
    },
    dataType: 'jsonp',
    jsonpCallback: 'jsonFlickrApi',
    success: function (data) {
      var stat = data.stat || "fail";

      if (stat !== "fail")
      {
        app.add_flickr_set (data.photoset);
      }
    }
  });
};

app.add_flickr_set = function (set)
{
  if (! set)
    return;

  var owner = set.ownername || "";
  var ownerurl = "http://www.flickr.com/photos/" + set.owner;
  var photos = set.photo || [];

  photos.forEach(function (photo) {
    app.add_flickr_photo (photo, owner, ownerurl);
  });

};

app.add_flickr_photo = function (photo, owner, ownerurl)
{
  var ph_id = photo.id || "";
  var photo_title = photo.title || "";
  var photo_secret = photo.secret || "";

  console.log (ph_id);

  $.ajax (
    {
      type: "GET",
      url: "https://api.flickr.com/services/rest/",
      data: {
        method : "flickr.photos.getSizes",
        api_key: app.flickr_key (),
        photo_id: ph_id,
        format: "json",
          jsoncallback: 'sizes_func_api' + ph_id
      },
      dataType: 'jsonp',
      jsonpCallback: 'sizes_func_api' + ph_id,
    }
  ).done (function (sizes_data) {

    $.ajax (
      {
        type: "GET",
        url: "https://api.flickr.com/services/rest/",
        data: {
          method : "flickr.photos.getInfo",
          api_key: app.flickr_key (),
          photo_id: ph_id,
          secret: photo_secret,
          format: "json",
          jsoncallback: "info_func_api" + ph_id
        },
        dataType: 'jsonp',
        jsonpCallback: 'info_func_api' + ph_id,
      }
    ).done (function (info_data) {

        if (sizes_data.stat !== 'ok')
        {
          console.log(sizes_data.message);
        }

        if (info_data.stat !== 'ok')
        {
          console.log(info_data.message);
        }

        var sizes = sizes_data.sizes || {};
        var photo_info = info_data.photo || {};

        var photo_url = sizes.size[Math.floor(sizes.size.length / 2)].source;
        var photo_licence = photo_info.license;

        console.log (info_data);

        app.add_photo(photo_title, photo_url, owner, ownerurl, photo_licence);
      });
  });
};

app.add_from_gallery = function ()
{
  var autor = $("#gallery_autor").val();
  var autor_url = $("#gallery_autor_url").val();
  var licence = $("#gallery_licence").val();

  var uploader = wp.media ({
        title : "Subir foto",
        button : {
          text : "Engadir Foto"
        },
        library : {
          type : 'image',
        },
        multiple : true,
        frame : 'select',
      });

  uploader.on ('select',
    function ()
    {
      var selection = uploader.state().get('selection');

      selection.map(
        function(att)
        {
          var attachment = att.toJSON();
          var img_url = attachment.sizes.full.url;
          var img_title = attachment.title;

          app.add_photo (img_title, img_url, autor, licence);

        });
    }
  );

  uploader.open();
};

app.add_from_url = function ()
{
  var title =  $("#url_titulo").val ();
  var url = $("#url_url").val();
  var autor = $("#url_autor").val();
  var url_autor = $("#url_url_autor").val ();
  var licence = $("#url_licence").val();

  app.add_photo (title, url, autor, url_autor, licence);
};

app.add_from_facebook = function ()
{
  var albumid =  $("#fb_input").val ();
  var licence = $("#fb_licence").val();

  FB.login(
    function(response)
    {
        if (response.authResponse)
        {
          console.log('Welcome!  Fetching your information.... ');
          FB.api(
            "/" + albumid + "/photos",
            function (response)
            {
              if (response.error)
              {
                console.log (response.error.message);
                return;
              }

              response.data.forEach (function (photo)
                {
                  var title = photo.name;
                  var url = photo.images[0].source;
                  var owner = photo.from.name;
                  var owner_url = "http://facebook.com/" +  photo.from.id;

                  app.add_photo(title, url, owner, owner_url, licence);
                }
              );
            }
          );
        }
        else
        {
          console.log('User cancelled login or did not fully authorize.');
        }
    },
    {
      scope: "user_photos"
    }
  );
};

app.add_photo = function (photo_title, photo_url, photo_owner, photo_owner_url, photo_licence)
{

  app.delete_photo (photo_url);

  var new_photo = {
    title:photo_title,
    url: photo_url,
    owner: photo_owner,
    ownerurl: photo_owner_url,
    licence: photo_licence
  };

  //UPDATE INPUT
  var photos = JSON.parse($("#photos_hidden_input").val());
  photos.push (new_photo);
  $("#photos_hidden_input").val (JSON.stringify (photos));

  //UPDATE TABLE
  var source = '<tr><td><img class="photo" height="200px" src="{{photo_url}}"/></td><td><a href="{{photo_owner_url}}">' +
    '{{photo_owner}}</a></td><td><img width="80px" src="{{img_licence}}"/></td><td><a href="JavaScript:app.delete_photo(\'{{photo_url}}\', true)">Borrar</a></td></tr>';
  var template = Handlebars.compile(source);

  html = template ({
        photo_title: new_photo.title,
        photo_url: new_photo.url,
        photo_owner: new_photo.owner,
        photo_owner_url: new_photo.ownerurl,
        img_licence: app.licences_images[new_photo.licence]
      });

  $("#photos_list tbody").append(html);
};


app.delete_photo = function (url)
{

  //UPDATE INPUT
  var photos = JSON.parse($("#photos_hidden_input").val());
  var new_photos = photos.filter (function (x) {
    return x.url !== url;
  });

  $("#photos_hidden_input").val(JSON.stringify (new_photos));

  //UPDATE TABLE
  var imgs = $("#photos_list tbody img.photo");

  for(var key in imgs)
  {
    if (imgs.hasOwnProperty(key)  &&
        /^0$|^[1-9]\d*$/.test(key) &&
        key <= 4294967294
        )
    {
      if (imgs[key].src === url)
      {
        $("#photos_list tbody tr")[key].remove ();
        break;
      }
    }
  }

};

app.reload_photos = function ()
{
  var photos = JSON.parse($("#photos_hidden_input").val());

  var source = '<tr><td><img class="photo" height="200px" src="{{photo_url}}"/></td><td><a href="{{photo_owner_url}}">' +
    '{{photo_owner}}</a></td><td><img width="80px" src="{{img_licence}}"/></td><td><a href="JavaScript:app.delete_photo(\'{{photo_url}}\', true)">Borrar</a></td></tr>';
  var template = Handlebars.compile(source);
  var html = '';

  $("#photos_list tbody").html ("");

  for(var key in photos)
  {
    if (photos.hasOwnProperty(key)  &&
        /^0$|^[1-9]\d*$/.test(key) &&
        key <= 4294967294
        )
    {
      var photo = photos[key];
      html = template ({
        photo_title: photo.title,
        photo_url: photo.url,
        photo_owner: photo.owner,
        photo_owner_url: photo.ownerurl
      });

      $("#photos_list tbody").append(html);
    }
  }
};



jQuery(document).ready(function($)
{
  app.init_fb_api ();
  app.reload_photos ();
});