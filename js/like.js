/*! JQuery Ajax ReaCtions for JohnCMS By MrT98
// NhanhNao.Xyz Team CMS
// Copyright by MrT98
// Mọi thắc mắc và hỗ trợ tại http://nhanhnao.xyz và http://phieubac.ga
*/

// JQuery Document
$(document).ready(function() {
  $.base_url = 'http://localhost:8888/'; // Your website name

$('.like-pit').on('click', function(e) {
  // Change reaction icon
  if ($(this).attr('class') == 'like-pit first_click') {
    $('.first_click_wrap_content').children().show();
  } else {
    $('.second_click_content_wrap').children().hide();
  }
});
// Like and Unlike

var count = 0;
$('body').on("click", '.like_button', function() {
  //$(this).closest('.new_like_items').children().hide();  
  var dataid = $(this).attr('data-id');
  var id = $(this).closest('.new_like').attr('id');
  var class_name = $(this).find(".icon-newL").attr("class");
  class_name = class_name.replace(/icon\-newL\s+/gi, "");
  $(this).closest(".new_like").find(".icon-lpn").removeClass().addClass("icon-lpn " + class_name);
  var count = 0;
  var KEY = parseInt($(this).attr("data"));
  var ID = $(this).attr("id");
  if (KEY == '1') {
    var sid = ID.split(/likes|loves|hahas|hihis|wowws|crys|angrys/);
  } else {
    var sid = ID.split(/like|love|haha|hihi|woww|cry|angry/);
  }
  var New_ID = sid[1];
  var REL = $(this).attr("rel");
  var datarequest = $(this).attr("data-request");

  var URL = '/like_post.php';
  var dataString = 'msg_id=' + New_ID + '&rel=' + REL + '&req=' + datarequest;
  $.ajax({
    type: "POST",
    url: URL,
    data: dataString,
    cache: false,
    success: function(html) {
      if (html) {
        // Like Started
        if (REL == 'Like') {
          count++;
          if ($('#likess' + New_ID).css('display') == 'none') {
            $("#likess" + New_ID).show('slow');
          }
          $('#' + ID).html('<div class="icon-newL icon-like-new"></div>').attr('rel', 'UnLike').attr('title', 'UnLike');
          $('#like_count' + New_ID).show('slow');
          if ($('#lcl' + New_ID).text() > 0) {
            $('#lcl' + New_ID).html(function(i, val) {
              return val * 1 + 1
            });
          } else {
            $("#elikes" + New_ID).show('slow').prepend("<span id='like_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-like-new lpos' id='clk" + New_ID + "'></div><div class='lcl' id='lcl" + New_ID + "'>" + count + "</div></span>");
          }
          if ($("#love" + New_ID).attr('rel') == 'UnLove' && $("#love" + New_ID).attr('title') == 'UnLove') {
            $('#love' + New_ID).html('<div class="icon-newL icon-love-new"></div>').attr('rel', 'Love').attr('title', 'Love');
            $('#lco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });
            if ($('#lco' + New_ID).text() == 0) {
              $('#love_count' + New_ID).remove();
              $("#eloves" + New_ID).hide('slow');
              $("#love_count" + New_ID).hide('slow').prepend("<span id='love_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-love-new lpos'></div><div class='lco' id='lco" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#lco' + New_ID).text() <= 0) {
              $("#eloves" + New_ID).hide('slow');
            }
          }
          if ($('#haha' + New_ID).attr('rel') == 'UnHaha' && $('#haha' + New_ID).attr('title') == 'UnHaha') {
            $('#haha' + New_ID).html('<div class="icon-newL icon-haha-new"></div>').attr('rel', 'Haha').attr('title', 'Haha');
            $('#hco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });
            if ($('#hco' + New_ID).text() == 0) {
              $('#haha_count' + New_ID).remove();
              $("#ehaha" + New_ID).hide('slow');
              $("#haha_count" + New_ID).hide('slow').prepend("<span id='haha_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-haha-new lpos'></div><div class='hco' id='hco" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#hco' + New_ID).text() <= 0) {
              $("#ehaha" + New_ID).hide('slow');
            }
          }
          if ($('#hihi' + New_ID).attr('rel') == 'UnHihi' && $('#hihi' + New_ID).attr('title') == 'UnHihi') {
            $('#hihi' + New_ID).html('<div class="icon-newL icon-mmmm-new"></div>').attr('rel', 'Hihi').attr('title', 'Hihi');
            $('#hico' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });
            if ($('#hico' + New_ID).text() == 0) {
              $('#hihi_count' + New_ID).remove();
              $("#ehihi" + New_ID).hide('slow');
              $("#hihi_count" + New_ID).hide('slow').prepend("<span id='hihi_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-mmm-new lpos'></div><div class='hico' id='hico" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#hico' + New_ID).text() <= 0) {
              $("#ehihi" + New_ID).hide('slow');
            }
          }
          if ($('#woww' + New_ID).attr('rel') == 'UnWoww' && $('#woww' + New_ID).attr('title') == 'UnWoww') {
            $('#woww' + New_ID).html('<div class="icon-newL icon-wowww-new"></div>').attr('rel', 'Woww').attr('title', 'Woww');
            $('#wco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });

            if ($('#wco' + New_ID).text() == 0) {
              $('#woww_count' + New_ID).remove();
              $("#ewoww" + New_ID).hide('slow');
              $("#woww_count" + New_ID).hide('slow').prepend("<span id='woww_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-wowww-new lpos'></div><div class='wico' id='wico" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#wco' + New_ID).text() <= 0) {
              $("#ewoww" + New_ID).hide('slow');
            }
          }
          if ($('#cry' + New_ID).attr('rel') == 'UnCry' && $('#cry' + New_ID).attr('title') == 'UnCry') {
            $('#cry' + New_ID).html('<div class="icon-newL icon-crying-new"></div>').attr('rel', 'Cry').attr('title', 'Cry');
            $('#cco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });

            if ($('#cco' + New_ID).text() == 0) {
              $('#cry_count' + New_ID).remove();
              $("#ecry" + New_ID).hide('slow');
              $("#cry_count" + New_ID).hide('slow').prepend("<span id='cry_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-crying-new lpos'></div><div class='cco' id='cco" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#cco' + New_ID).text() <= 0) {
              $("#ecry" + New_ID).hide('slow');
            }
          }
          if ($('#angry' + New_ID).attr('rel') == 'UnAngry' && $('#angry' + New_ID).attr('title') == 'UnAngry') {
            $('#angry' + New_ID).html('<div class="icon-newL icon-angry-new"></div>').attr('rel', 'Angry').attr('title', 'Angry');
            $('#eco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });

            if ($('#eco' + New_ID).text() == 0) {
              $('#angry_count' + New_ID).remove();
              $("#eangry" + New_ID).hide('slow');
              $("#angry_count" + New_ID).hide('slow').prepend("<span id='angry_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-angry-new lpos'></div><div class='eco' id='eco" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#eco' + New_ID).text() <= 0) {
              $("#eangry" + New_ID).hide('slow');
            }
          }
        } else if (REL == 'UnLike') {
          $('#ulk' + New_ID).removeClass('icon-like-new').addClass('icon-like-blf');
          $('#lcl' + New_ID).text(function(i, val) {
            return val * 1 - 1
          });
          if ($('#lcl' + New_ID).text() == 0) {
            $("#elikes" + New_ID).hide('slow');
            $('#like_count' + New_ID).remove();
          }
          if ($('#ehaha' + New_ID).css('display') == 'none' && $('#ehihi' + New_ID).css('display') == 'none' && $('#ewoww' + New_ID).css('display') == 'none' && $('#ecry' + New_ID).css('display') == 'none' && $('#eangrys' + New_ID).css('display') == 'none') {
            if ($('#lcl' + New_ID).text() < 0) {
              $("#likess" + New_ID).hide('slow');
            }

          }
          $('#' + ID).html('<div class="icon-newL icon-like-new"></div>').attr('rel', 'Like').attr('title', 'Like');
        }
        // Like Finished
        //Love Started
        if (REL == 'Love') {
          count++;
          if ($('#likess' + New_ID).css('display') == 'none') {
            $("#likess" + New_ID).show('slow');
          }
          $('#' + ID).html('<div class="icon-newL icon-love-new"></div>').attr('rel', 'UnLove').attr('title', 'UnLove');
          $('#love_count' + New_ID).show('slow');
          if ($('#lco' + New_ID).text() > 0) {
            $('#lco' + New_ID).html(function(i, val) {
              return val * 1 + 1
            });
          } else {
            $("#eloves" + New_ID).show('slow').prepend("<span id='love_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-love-new lpos' id='llk" + New_ID + "'></div><div class='lco' id='lco" + New_ID + "'>" + count + "</div></span>");
          }
          if ($('#like' + New_ID).attr('rel') == 'UnLike' && $('#like' + New_ID).attr('title') == 'UnLike') {
            $('#like' + New_ID).html('<div class="icon-newL icon-like-new"></div>').attr('rel', 'Like').attr('title', 'Like');
            $('#lcl' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });
            if ($('#lcl' + New_ID).text() == 0) {
              $('#like_count' + New_ID).remove();
              $("#elikes" + New_ID).hide('slow');
              $("#like_count" + New_ID).hide('slow').prepend("<span id='like_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-like-new lpos'></div><div class='lcl' id='lcl" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#lcl' + New_ID).text() <= 0) {
              $("#elikes" + New_ID).hide('slow');
            }
          }
          if ($('#haha' + New_ID).attr('rel') == 'UnHaha' && $('#haha' + New_ID).attr('title') == 'UnHaha') {
            $('#haha' + New_ID).html('<div class="icon-newL icon-haha-new"></div>').attr('rel', 'Haha').attr('title', 'Haha');
            $('#hco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });

            if ($('#hco' + New_ID).text() == 0) {
              $('#haha_count' + New_ID).remove();
              $("#ehaha" + New_ID).hide('slow');
              console.log('removedLikeaaaa');
              $("#haha_count" + New_ID).hide('slow').prepend("<span id='haha_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-haha-new lpos'></div><div class='hco' id='hco" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#hco' + New_ID).text() <= 0) {
              $("#ehaha" + New_ID).hide('slow');
            }
          }
          if ($('#hihi' + New_ID).attr('rel') == 'UnHihi' && $('#hihi' + New_ID).attr('title') == 'UnHihi') {
            $('#hihi' + New_ID).html('<div class="icon-newL icon-mmmm-new"></div>').attr('rel', 'Hihi').attr('title', 'Hihi');
            $('#hico' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });

            if ($('#hico' + New_ID).text() == 0) {
              $('#hihi_count' + New_ID).remove();
              $("#ehihi" + New_ID).hide('slow');
              $("#hihi_count" + New_ID).hide('slow').prepend("<span id='hihi_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-mmm-new lpos'></div><div class='hico' id='hico" + New_ID + "'>" + count + "</div></span>");
            }
          }
          if ($('#woww' + New_ID).attr('rel') == 'UnWoww' && $('#woww' + New_ID).attr('title') == 'UnWoww') {
            $('#woww' + New_ID).html('<div class="icon-newL icon-wowww-new"></div>').attr('rel', 'Woww').attr('title', 'Woww');
            $('#wco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });
            if ($('#wco' + New_ID).text() == 0) {
              $('#woww_count' + New_ID).remove();
              $("#ewoww" + New_ID).hide('slow');
              $("#woww_count" + New_ID).hide('slow').prepend("<span id='woww_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-wowww-new lpos'></div><div class='wico' id='wico" + New_ID + "'>" + count + "</div></span>");
            }
          }
          if ($('#cry' + New_ID).attr('rel') == 'UnCry' && $('#cry' + New_ID).attr('title') == 'UnCry') {
            $('#cry' + New_ID).html('<div class="icon-newL icon-crying-new"></div>').attr('rel', 'Cry').attr('title', 'Cry');
            $('#cco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });
            if ($('#cco' + New_ID).text() == 0) {
              $('#cry_count' + New_ID).remove();
              $("#ecry" + New_ID).hide('slow');
              $("#cry_count" + New_ID).hide('slow').prepend("<span id='cry_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-crying-new lpos'></div><div class='cco' id='cco" + New_ID + "'>" + count + "</div></span>");
            }
          }
          if ($('#angry' + New_ID).attr('rel') == 'UnAngry' && $('#angry' + New_ID).attr('title') == 'UnAngry') {
            $('#angry' + New_ID).html('<div class="icon-newL icon-angry-new"></div>').attr('rel', 'Angry').attr('title', 'Angry');
            $('#eco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });
            if ($('#eco' + New_ID).text() == 0) {
              $('#angry_count' + New_ID).remove();
              $("#eangrys" + New_ID).hide('slow');
              $("#angry_count" + New_ID).hide('slow').prepend("<span id='angry_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-angry-new lpos'></div><div class='eco' id='eco" + New_ID + "'>" + count + "</div></span>");
            }
          }
        } else if (REL == 'UnLove') {
          $('#ulk' + New_ID).removeClass('icon-love-new').addClass('icon-like-blf');
          $('#lco' + New_ID).text(function(i, val) {
            return val * 1 - 1
          });
          if ($('#lco' + New_ID).text() == 0) {
            $("#eloves" + New_ID).hide('slow');
            $('#love_count' + New_ID).remove();
          }
          if ($('#elikes' + New_ID).css('display') == 'none' && $('#ehaha' + New_ID).css('display') == 'none' && $('#ehihi' + New_ID).css('display') == 'none' && $('#ewoww' + New_ID).css('display') == 'none' && $('#ecry' + New_ID).css('display') == 'none' && $('#eangrys' + New_ID).css('display') == 'none') {
            if ($('#lco' + New_ID).text() < 1) {
              $("#likess" + New_ID).hide('slow');
            }

          }
          $('#' + ID).html('<div class="icon-newL icon-love-new"></div>').attr('rel', 'Love').attr('title', 'Love');
        }
        //Love Finished
        //Haha Started
        if (REL == 'Haha') {
          count++;
          if ($('#likess' + New_ID).css('display') == 'none') {
            $("#likess" + New_ID).show('slow');
          }
          $('#' + ID).html('<div class="icon-newL icon-haha-new"></div>').attr('rel', 'UnHaha').attr('title', 'UnHaha');
          $('#haha_count' + New_ID).show('slow');
          if ($('#hco' + New_ID).text() > 0) {
            $('#hco' + New_ID).html(function(i, val) {
              return val * 1 + 1
            });
          } else {
            $("#ehaha" + New_ID).show('slow').prepend("<span id='haha_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-haha-new lpos' id='hlk" + New_ID + "'></div><div class='hco' id='hco" + New_ID + "'>" + count + "</div></span>");
          }
          if ($('#like' + New_ID).attr('rel') == 'UnLike' && $('#like' + New_ID).attr('title') == 'UnLike') {
            $('#like' + New_ID).html('<div class="icon-newL icon-like-new"></div>').attr('rel', 'Like').attr('title', 'Like');
            $('#lcl' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });
            if ($('#lcl' + New_ID).text() == 0) {
              $('#like_count' + New_ID).remove();
              $("#elikes" + New_ID).hide('slow');
              $("#like_count" + New_ID).hide('slow').prepend("<span id='like_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-like-new lpos'></div><div class='lcl' id='lcl" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#lcl' + New_ID).text() <= 0) {
              $("#elikes" + New_ID).hide('slow');
            }
          }
          if ($('#love' + New_ID).attr('rel') == 'UnLove' && $('#love' + New_ID).attr('title') == 'UnLove') {
            $('#love' + New_ID).html('<div class="icon-newL icon-love-new"></div>').attr('rel', 'Love').attr('title', 'Love');
            $('#lco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });

            if ($('#lco' + New_ID).text() == 0) {
              $('#love_count' + New_ID).remove();
              $("#eloves" + New_ID).hide('slow');
              $("#love_count" + New_ID).hide('slow').prepend("<span id='love_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-love-new lpos'></div><div class='lco' id='lco" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#lco' + New_ID).text() <= 0) {
              $("#eloves" + New_ID).hide('slow');
            }
          }
          if ($('#hihi' + New_ID).attr('rel') == 'UnHihi' && $('#hihi' + New_ID).attr('title') == 'UnHihi') {
            $('#hihi' + New_ID).html('<div class="icon-newL icon-mmmm-new"></div>').attr('rel', 'Hihi').attr('title', 'Hihi');
            $('#hico' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });

            if ($('#hico' + New_ID).text() == 0) {
              $('#hihi_count' + New_ID).remove();
              $("#ehihi" + New_ID).hide('slow');
              $("#hihi_count" + New_ID).hide('slow').prepend("<span id='hihi_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-mmmm-new lpos'></div><div class='hico' id='hico" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#hico' + New_ID).text() <= 0) {
              $("#ehihi" + New_ID).hide('slow');
            }
          }
          if ($('#woww' + New_ID).attr('rel') == 'UnWoww' && $('#woww' + New_ID).attr('title') == 'UnWoww') {
            $('#woww' + New_ID).html('<div class="icon-newL icon-wowww-new"></div>').attr('rel', 'Woww').attr('title', 'Woww');
            $('#wco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });

            if ($('#wco' + New_ID).text() == 0) {
              $('#woww_count' + New_ID).remove();
              $("#ewoww" + New_ID).hide('slow');
              $("#woww_count" + New_ID).hide('slow').prepend("<span id='woww_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-wowww-new lpos'></div><div class='wico' id='wico" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#wco' + New_ID).text() <= 0) {
              $("#ewoww" + New_ID).hide('slow');
            }
          }
          if ($('#cry' + New_ID).attr('rel') == 'UnCry' && $('#cry' + New_ID).attr('title') == 'UnCry') {
            $('#cry' + New_ID).html('<div class="icon-newL icon-crying-new"></div>').attr('rel', 'Cry').attr('title', 'Cry');
            $('#cco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });

            if ($('#cco' + New_ID).text() == 0) {
              $('#cry_count' + New_ID).remove();
              $("#ecry" + New_ID).hide('slow');
              $("#cry_count" + New_ID).hide('slow').prepend("<span id='cry_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-crying-new lpos'></div><div class='cco' id='cco" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#cco' + New_ID).text() <= 0) {
              $("#ecry" + New_ID).hide('slow');
            }
          }
          if ($('#angry' + New_ID).attr('rel') == 'UnAngry' && $('#angry' + New_ID).attr('title') == 'UnAngry') {
            $('#angry' + New_ID).html('<div class="icon-newL icon-angry-new"></div>').attr('rel', 'Angry').attr('title', 'Angry');
            $('#eco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });

            if ($('#eco' + New_ID).text() == 0) {
              $('#angry_count' + New_ID).remove();
              $("#eangry" + New_ID).hide('slow');
              $("#angry_count" + New_ID).hide('slow').prepend("<span id='angry_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-angry-new lpos'></div><div class='eco' id='eco" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#eco' + New_ID).text() <= 0) {
              $("#eangry" + New_ID).hide('slow');
            }
          }

        } else if (REL == 'UnHaha') {
          $('#ulk' + New_ID).removeClass('icon-haha-new').addClass('icon-like-blf');
          $('#hco' + New_ID).text(function(i, val) {
            return val * 1 - 1
          });
          if ($('#hco' + New_ID).text() == 0) {
            $("#ehaha" + New_ID).hide('slow');
            $('#haha_count' + New_ID).remove();
          }
          if ($('#eloves' + New_ID).css('display') == 'none' && $('#elikes' + New_ID).css('display') == 'none' && $('#ehihi' + New_ID).css('display') == 'none' && $('#ewoww' + New_ID).css('display') == 'none' && $('#ecry' + New_ID).css('display') == 'none' && $('#eangrys' + New_ID).css('display') == 'none') {
            if ($('#hco' + New_ID).text() < 1) {
              $("#likess" + New_ID).hide('slow');
            }

          }
          $('#' + ID).html('<div class="icon-newL icon-haha-new"></div>').attr('rel', 'Haha').attr('title', 'Haha');
        }
        //Haha Finished
        //Hihi Started
        if (REL == 'Hihi') {
          count++;
          if ($('#likess' + New_ID).css('display') == 'none') {
            $("#likess" + New_ID).show('slow');
          }
          $('#' + ID).html('<div class="icon-newL icon-mmmm-new"></div>').attr('rel', 'UnHihi').attr('title', 'UnHihi');
          $('#hihi_count' + New_ID).show('slow');
          if ($('#hico' + New_ID).text() > 0) {
            $('#hico' + New_ID).html(function(i, val) {
              return val * 1 + 1
            });
          } else {
            $("#ehihi" + New_ID).show('slow').prepend("<span id='hihi_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-mmmm-new lpos' id='llk" + New_ID + "'></div><div class='hico' id='hico" + New_ID + "'>" + count + "</div></span>");
          }

          if ($('#like' + New_ID).attr('rel') == 'UnLike' && $('#like' + New_ID).attr('title') == 'UnLike') {
            $('#like' + New_ID).html('<div class="icon-newL icon-like-new"></div>').attr('rel', 'Like').attr('title', 'Like');
            $('#lcl' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });
            if ($('#lcl' + New_ID).text() == 0) {
              $('#like_count' + New_ID).remove();
              $("#elikes" + New_ID).hide('slow');
              $("#like_count" + New_ID).hide('slow').prepend("<span id='like_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-like-new lpos'></div><div class='lcl' id='lcl" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#lcl' + New_ID).text() <= 0) {
              $("#elikes" + New_ID).hide('slow');
            }
          }
          if ($('#haha' + New_ID).attr('rel') == 'UnHaha' && $('#haha' + New_ID).attr('title') == 'UnHaha') {
            $('#haha' + New_ID).html('<div class="icon-newL icon-haha-new"></div>').attr('rel', 'Haha').attr('title', 'Haha');
            $('#hco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });

            if ($('#hco' + New_ID).text() == 0) {
              $('#haha_count' + New_ID).remove();
              $("#ehaha" + New_ID).hide('slow');
              $("#haha_count" + New_ID).hide('slow').prepend("<span id='haha_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-haha-new lpos'></div><div class='hco' id='hco" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#hco' + New_ID).text() <= 0) {
              $("#ehaha" + New_ID).hide('slow');
            }
          }
          if ($('#love' + New_ID).attr('rel') == 'UnLove' && $('#love' + New_ID).attr('title') == 'UnLove') {
            $('#love' + New_ID).html('<div class="icon-newL icon-love-new"></div>').attr('rel', 'Love').attr('title', 'Love');
            $('#lco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });

            if ($('#lco' + New_ID).text() == 0) {
              $('#love_count' + New_ID).remove();
              $("#eloves" + New_ID).hide('slow');
              $("#love_count" + New_ID).hide('slow').prepend("<span id='love_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-love-new lpos'></div><div class='lco' id='lco" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#lco' + New_ID).text() <= 0) {
              $("#eloves" + New_ID).hide('slow');
            }
          }
          if ($('#woww' + New_ID).attr('rel') == 'UnWoww' && $('#woww' + New_ID).attr('title') == 'UnWoww') {
            $('#woww' + New_ID).html('<div class="icon-newL icon-wowww-new"></div>').attr('rel', 'Woww').attr('title', 'Woww');
            $('#wco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });

            if ($('#wco' + New_ID).text() == 0) {
              $('#woww_count' + New_ID).remove();
              $("#ewoww" + New_ID).hide('slow');
              $("#woww_count" + New_ID).hide('slow').prepend("<span id='woww_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-wowww-new lpos'></div><div class='wico' id='wico" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#wco' + New_ID).text() <= 0) {
              $("#ewoww" + New_ID).hide('slow');
            }
          }
          if ($('#cry' + New_ID).attr('rel') == 'UnCry' && $('#cry' + New_ID).attr('title') == 'UnCry') {
            $('#cry' + New_ID).html('<div class="icon-newL icon-crying-new"></div>').attr('rel', 'Cry').attr('title', 'Cry');
            $('#cco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });

            if ($('#cco' + New_ID).text() == 0) {
              $('#cry_count' + New_ID).remove();
              $("#ecry" + New_ID).hide('slow');
              $("#cry_count" + New_ID).hide('slow').prepend("<span id='cry_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-crying-new lpos'></div><div class='cco' id='cco" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#cco' + New_ID).text() <= 0) {
              $("#ecry" + New_ID).hide('slow');
            }
          }
          if ($('#angry' + New_ID).attr('rel') == 'UnAngry' && $('#angry' + New_ID).attr('title') == 'UnAngry') {
            $('#angry' + New_ID).html('<div class="icon-newL icon-angry-new"></div>').attr('rel', 'Angry').attr('title', 'Angry');
            $('#eco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });

            if ($('#eco' + New_ID).text() == 0) {
              $('#angry_count' + New_ID).remove();
              $("#eangry" + New_ID).hide('slow');
              $("#angry_count" + New_ID).hide('slow').prepend("<span id='angry_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-angry-new lpos'></div><div class='eco' id='eco" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#eco' + New_ID).text() <= 0) {
              $("#eangry" + New_ID).hide('slow');
            }
          }
        } else if (REL == 'UnHihi') {
          $('#ulk' + New_ID).removeClass('icon-mmmm-new').addClass('icon-like-blf');
          $('#hico' + New_ID).text(function(i, val) {
            return val * 1 - 1
          });
          if ($('#hico' + New_ID).text() == 0) {
            $("#ehihi" + New_ID).hide('slow');
            $('#hihi_count' + New_ID).remove();
          }
          if ($('#eloves' + New_ID).css('display') == 'none' && $('#elikes' + New_ID).css('display') == 'none' && $('#ehaha' + New_ID).css('display') == 'none' && $('#ewoww' + New_ID).css('display') == 'none' && $('#ecry' + New_ID).css('display') == 'none' && $('#eangrys' + New_ID).css('display') == 'none') {
            if ($('#hico' + New_ID).text() < 1) {
              $("#likess" + New_ID).hide('slow');
            }
          }
          $('#' + ID).html('<div class="icon-newL icon-mmmm-new"></div>').attr('rel', 'Hihi').attr('title', 'Hihi');
        }
        //Hihi Finished
        //Woww Started
        if (REL == 'Woww') {
          count++;
          if ($('#wowws' + New_ID).css('display') == 'none') {
            $("#wowws" + New_ID).show('slow');
          }
          $('#' + ID).html('<div class="icon-newL icon-wowww-new"></div>').attr('rel', 'UnWoww').attr('title', 'UnWoww');
          $('#woww_count' + New_ID).show('slow');
          if ($('#wco' + New_ID).text() > 0) {
            $('#wco' + New_ID).html(function(i, val) {
              return val * 1 + 1
            });
          } else {
            $("#ewoww" + New_ID).show('slow').prepend("<span id='woww_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-wowww-new lpos' id='woow" + New_ID + "'></div><div class='wco' id='wco" + New_ID + "'>" + count + "</div></span>");
          }
          if ($("#love" + New_ID).attr('rel') == 'UnLove' && $("#love" + New_ID).attr('title') == 'UnLove') {
            $('#love' + New_ID).html('<div class="icon-newL icon-love-new"></div>').attr('rel', 'Love').attr('title', 'Love');
            $('#lco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });
            if ($('#lco' + New_ID).text() == 0) {
              $('#love_count' + New_ID).remove();
              $("#eloves" + New_ID).hide('slow');
              $("#love_count" + New_ID).hide('slow').prepend("<span id='love_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-love-new lpos'></div><div class='lco' id='lco" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#lco' + New_ID).text() <= 0) {
              $("#eloves" + New_ID).hide('slow');
            }
          }
          if ($('#haha' + New_ID).attr('rel') == 'UnHaha' && $('#haha' + New_ID).attr('title') == 'UnHaha') {
            $('#haha' + New_ID).html('<div class="icon-newL icon-haha-new"></div>').attr('rel', 'Haha').attr('title', 'Haha');
            $('#hco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });

            if ($('#hco' + New_ID).text() == 0) {
              $('#haha_count' + New_ID).remove();
              $("#ehaha" + New_ID).hide('slow');
              $("#haha_count" + New_ID).hide('slow').prepend("<span id='haha_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-haha-new lpos'></div><div class='hco' id='hco" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#hco' + New_ID).text() <= 0) {
              $("#ehaha" + New_ID).hide('slow');
            }
          }
          if ($('#hihi' + New_ID).attr('rel') == 'UnHihi' && $('#hihi' + New_ID).attr('title') == 'UnHihi') {
            $('#hihi' + New_ID).html('<div class="icon-newL icon-mmmm-new"></div>').attr('rel', 'Hihi').attr('title', 'Hihi');
            $('#hico' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });

            if ($('#hico' + New_ID).text() == 0) {
              $('#hihi_count' + New_ID).remove();
              $("#ehihi" + New_ID).hide('slow');
              $("#hihi_count" + New_ID).hide('slow').prepend("<span id='hihi_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-mmm-new lpos'></div><div class='hico' id='hico" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#hico' + New_ID).text() <= 0) {
              $("#ehihi" + New_ID).hide('slow');
            }
          }
          if ($('#like' + New_ID).attr('rel') == 'UnLike' && $('#like' + New_ID).attr('title') == 'UnLike') {
            $('#like' + New_ID).html('<div class="icon-newL icon-like-new"></div>').attr('rel', 'Like').attr('title', 'Like');
            $('#lcl' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });
            if ($('#lcl' + New_ID).text() == 0) {
              $('#like_count' + New_ID).remove();
              $("#elikes" + New_ID).hide('slow');
              $("#like_count" + New_ID).hide('slow').prepend("<span id='like_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-like-new lpos'></div><div class='lcl' id='lcl" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#lcl' + New_ID).text() <= 0) {
              $("#elikes" + New_ID).hide('slow');
            }
          }
          if ($('#cry' + New_ID).attr('rel') == 'UnCry' && $('#cry' + New_ID).attr('title') == 'UnCry') {
            $('#cry' + New_ID).html('<div class="icon-newL icon-crying-new"></div>').attr('rel', 'Cry').attr('title', 'Cry');
            $('#cco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });

            if ($('#cco' + New_ID).text() == 0) {
              $('#cry_count' + New_ID).remove();
              $("#ecry" + New_ID).hide('slow');
              $("#cry_count" + New_ID).hide('slow').prepend("<span id='cry_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-crying-new lpos'></div><div class='cco' id='cco" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#cco' + New_ID).text() <= 0) {
              $("#ecry" + New_ID).hide('slow');
            }
          }
          if ($('#angry' + New_ID).attr('rel') == 'UnAngry' && $('#angry' + New_ID).attr('title') == 'UnAngry') {
            $('#angry' + New_ID).html('<div class="icon-newL icon-angry-new"></div>').attr('rel', 'Angry').attr('title', 'Angry');
            $('#eco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });

            if ($('#eco' + New_ID).text() == 0) {
              $('#angry_count' + New_ID).remove();
              $("#eangry" + New_ID).hide('slow');
              $("#angry_count" + New_ID).hide('slow').prepend("<span id='angry_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-angry-new lpos'></div><div class='eco' id='eco" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#eco' + New_ID).text() <= 0) {
              $("#eangry" + New_ID).hide('slow');
            }
          }
        } else if (REL == 'UnWoww') {
          $('#ulk' + New_ID).removeClass('icon-wowww-new').addClass('icon-like-blf');
          $('#wco' + New_ID).text(function(i, val) {
            return val * 1 - 1
          });
          if ($('#wco' + New_ID).text() == 0) {
            $("#wowws" + New_ID).hide('slow');
            $('#woww_count' + New_ID).remove();
          }
          if ($('#eloves' + New_ID).css('display') == 'none' && $('#ehaha' + New_ID).css('display') == 'none' && $('#ehihi' + New_ID).css('display') == 'none' && $('#elikes' + New_ID).css('display') == 'none' && $('#ecry' + New_ID).css('display') == 'none' && $('#eangrys' + New_ID).css('display') == 'none') {
            if ($('#wco' + New_ID).text() < 0) {
              $("#likess" + New_ID).hide('slow');
            }
          }

          $('#' + ID).html('<div class="icon-newL icon-wowww-new"></div>').attr('rel', 'Woww').attr('title', 'Woww');
        }
        //Woww Finished
        //Cry Started
        if (REL == 'Cry') {
          count++;
          if ($('#crys' + New_ID).css('display') == 'none') {
            $("#crys" + New_ID).show('slow');
          }
          $('#' + ID).html('<div class="icon-newL icon-crying-new"></div>').attr('rel', 'UnCry').attr('title', 'UnCry');
          $('#cry_count' + New_ID).show('slow');
          if ($('#cco' + New_ID).text() > 0) {
            $('#cco' + New_ID).html(function(i, val) {
              return val * 1 + 1
            });
          } else {
            $("#ecry" + New_ID).show('slow').prepend("<span id='cry_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-crying-new lpos' id='cry" + New_ID + "'></div><div class='cco' id='cco" + New_ID + "'>" + count + "</div></span>");
          }

          if ($("#love" + New_ID).attr('rel') == 'UnLove' && $("#love" + New_ID).attr('title') == 'UnLove') {
            $('#love' + New_ID).html('<div class="icon-newL icon-love-new"></div>').attr('rel', 'Love').attr('title', 'Love');
            $('#lco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });
            if ($('#lco' + New_ID).text() == 0) {
              $('#love_count' + New_ID).remove();
              $("#eloves" + New_ID).hide('slow');
              $("#love_count" + New_ID).hide('slow').prepend("<span id='love_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-love-new lpos'></div><div class='lco' id='lco" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#lco' + New_ID).text() <= 0) {
              $("#eloves" + New_ID).hide('slow');
            }
          }
          if ($('#haha' + New_ID).attr('rel') == 'UnHaha' && $('#haha' + New_ID).attr('title') == 'UnHaha') {
            $('#haha' + New_ID).html('<div class="icon-newL icon-haha-new"></div>').attr('rel', 'Haha').attr('title', 'Haha');
            $('#hco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });

            if ($('#hco' + New_ID).text() == 0) {
              $('#haha_count' + New_ID).remove();
              $("#ehaha" + New_ID).hide('slow');
              $("#haha_count" + New_ID).hide('slow').prepend("<span id='haha_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-haha-new lpos'></div><div class='hco' id='hco" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#hco' + New_ID).text() <= 0) {
              $("#ehaha" + New_ID).hide('slow');
            }
          }
          if ($('#hihi' + New_ID).attr('rel') == 'UnHihi' && $('#hihi' + New_ID).attr('title') == 'UnHihi') {
            $('#hihi' + New_ID).html('<div class="icon-newL icon-mmmm-new"></div>').attr('rel', 'Hihi').attr('title', 'Hihi');
            $('#hico' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });

            if ($('#hico' + New_ID).text() == 0) {
              $('#hihi_count' + New_ID).remove();
              $("#ehihi" + New_ID).hide('slow');
              $("#hihi_count" + New_ID).hide('slow').prepend("<span id='hihi_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-mmm-new lpos'></div><div class='hico' id='hico" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#hico' + New_ID).text() <= 0) {
              $("#ehihi" + New_ID).hide('slow');
            }
          }
          if ($('#like' + New_ID).attr('rel') == 'UnLike' && $('#like' + New_ID).attr('title') == 'UnLike') {
            $('#like' + New_ID).html('<div class="icon-newL icon-like-new"></div>').attr('rel', 'Like').attr('title', 'Like');
            $('#lcl' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });
            if ($('#lcl' + New_ID).text() == 0) {
              $('#like_count' + New_ID).remove();
              $("#elikes" + New_ID).hide('slow');
              $("#like_count" + New_ID).hide('slow').prepend("<span id='like_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-like-new lpos'></div><div class='lcl' id='lcl" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#lcl' + New_ID).text() <= 0) {
              $("#elikes" + New_ID).hide('slow');
            }
          }
          if ($('#woww' + New_ID).attr('rel') == 'UnWoww' && $('#woww' + New_ID).attr('title') == 'UnWoww') {
            $('#woww' + New_ID).html('<div class="icon-newL icon-wowww-new"></div>').attr('rel', 'Woww').attr('title', 'Woww');
            $('#wco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });

            if ($('#wco' + New_ID).text() == 0) {
              $('#woww_count' + New_ID).remove();
              $("#ewoww" + New_ID).hide('slow');
              $("#woww_count" + New_ID).hide('slow').prepend("<span id='woww_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-wowww-new lpos'></div><div class='wico' id='wico" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#wco' + New_ID).text() <= 0) {
              $("#ewoww" + New_ID).hide('slow');
            }
          }
          if ($('#angry' + New_ID).attr('rel') == 'UnAngry' && $('#angry' + New_ID).attr('title') == 'UnAngry') {
            $('#angry' + New_ID).html('<div class="icon-newL icon-angry-new"></div>').attr('rel', 'Angry').attr('title', 'Angry');
            $('#eco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });

            if ($('#eco' + New_ID).text() == 0) {
              $('#angry_count' + New_ID).remove();
              $("#eangry" + New_ID).hide('slow');
              $("#angry_count" + New_ID).hide('slow').prepend("<span id='angry_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-angry-new lpos'></div><div class='eco' id='eco" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#eco' + New_ID).text() <= 0) {
              $("#eangry" + New_ID).hide('slow');
            }
          }

        } else if (REL == 'UnCry') {
          $('#ulk' + New_ID).removeClass('icon-crying-new').addClass('icon-like-blf');
          $('#cco' + New_ID).text(function(i, val) {
            return val * 1 - 1
          });
          if ($('#cco' + New_ID).text() == 0) {
            $("#crys" + New_ID).hide('slow');
            $('#cry_count' + New_ID).remove();
          }
          if ($('#eloves' + New_ID).css('display') == 'none' && $('#ehaha' + New_ID).css('display') == 'none' && $('#ehihi' + New_ID).css('display') == 'none' && $('#elikes' + New_ID).css('display') == 'none' && $('#ewoww' + New_ID).css('display') == 'none' && $('#eangrys' + New_ID).css('display') == 'none') {
            if ($('#cco' + New_ID).text() < 0) {
              $("#likess" + New_ID).hide('slow');
            }
          }

          $('#' + ID).html('<div class="icon-newL icon-crying-new"></div>').attr('rel', 'Cry').attr('title', 'Cry');
        }
        //Cry finished
        //Angry Started
        if (REL == 'Angry') {
          count++;
          if ($('#likess' + New_ID).css('display') == 'none') {
            $("#likess" + New_ID).show('slow');
          }
          $('#' + ID).html('<div class="icon-newL icon-angry-new"></div>').attr('rel', 'UnAngry').attr('title', 'UnAngry');
          $('#angry_count' + New_ID).show('slow');
          if ($('#eco' + New_ID).text() > 0) {
            $('#eco' + New_ID).html(function(i, val) {
              return val * 1 + 1
            });
          } else {
            $("#eangrys" + New_ID).show('slow').prepend("<span id='angry_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-angry-new lpos' id='cry" + New_ID + "'></div><div class='eco' id='eco" + New_ID + "'>" + count + "</div></span>");
          }

          if ($("#love" + New_ID).attr('rel') == 'UnLove' && $("#love" + New_ID).attr('title') == 'UnLove') {
            $('#love' + New_ID).html('<div class="icon-newL icon-love-new"></div>').attr('rel', 'Love').attr('title', 'Love');
            $('#lco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });
            if ($('#lco' + New_ID).text() == 0) {
              $('#love_count' + New_ID).remove();
              $("#eloves" + New_ID).hide('slow');
              $("#love_count" + New_ID).hide('slow').prepend("<span id='love_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-love-new lpos'></div><div class='lco' id='lco" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#lco' + New_ID).text() <= 0) {
              $("#eloves" + New_ID).hide('slow');
            }
          }
          if ($('#haha' + New_ID).attr('rel') == 'UnHaha' && $('#haha' + New_ID).attr('title') == 'UnHaha') {
            $('#haha' + New_ID).html('<div class="icon-newL icon-haha-new"></div>').attr('rel', 'Haha').attr('title', 'Haha');
            $('#hco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });

            if ($('#hco' + New_ID).text() == 0) {
              $('#haha_count' + New_ID).remove();
              $("#ehaha" + New_ID).hide('slow');
              $("#haha_count" + New_ID).hide('slow').prepend("<span id='haha_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-haha-new lpos'></div><div class='hco' id='hco" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#hco' + New_ID).text() <= 0) {
              $("#ehaha" + New_ID).hide('slow');
            }
          }
          if ($('#hihi' + New_ID).attr('rel') == 'UnHihi' && $('#hihi' + New_ID).attr('title') == 'UnHihi') {
            $('#hihi' + New_ID).html('<div class="icon-newL icon-mmmm-new"></div>').attr('rel', 'Hihi').attr('title', 'Hihi');
            $('#hico' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });

            if ($('#hico' + New_ID).text() == 0) {
              $('#hihi_count' + New_ID).remove();
              $("#ehihi" + New_ID).hide('slow');
              $("#hihi_count" + New_ID).hide('slow').prepend("<span id='hihi_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-mmm-new lpos'></div><div class='hico' id='hico" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#hico' + New_ID).text() <= 0) {
              $("#ehihi" + New_ID).hide('slow');
            }
          }
          if ($('#like' + New_ID).attr('rel') == 'UnLike' && $('#like' + New_ID).attr('title') == 'UnLike') {
            $('#like' + New_ID).html('<div class="icon-newL icon-like-new"></div>').attr('rel', 'Like').attr('title', 'Like');
            $('#lcl' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });
            if ($('#lcl' + New_ID).text() == 0) {
              $('#like_count' + New_ID).remove();
              $("#elikes" + New_ID).hide('slow');
              $("#like_count" + New_ID).hide('slow').prepend("<span id='like_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-like-new lpos'></div><div class='lcl' id='lcl" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#lcl' + New_ID).text() <= 0) {
              $("#elikes" + New_ID).hide('slow');
            }
          }
          if ($('#woww' + New_ID).attr('rel') == 'UnWoww' && $('#woww' + New_ID).attr('title') == 'UnWoww') {
            $('#woww' + New_ID).html('<div class="icon-newL icon-wowww-new"></div>').attr('rel', 'Woww').attr('title', 'Woww');
            $('#wco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });

            if ($('#wco' + New_ID).text() == 0) {
              $('#woww_count' + New_ID).remove();
              $("#ewoww" + New_ID).hide('slow');
              $("#woww_count" + New_ID).hide('slow').prepend("<span id='woww_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-wowww-new lpos'></div><div class='wico' id='wico" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#wco' + New_ID).text() <= 0) {
              $("#ewoww" + New_ID).hide('slow');
            }
          }
          if ($('#cry' + New_ID).attr('rel') == 'UnCry' && $('#cry' + New_ID).attr('title') == 'UnCry') {
            $('#cry' + New_ID).html('<div class="icon-newL icon-crying-new"></div>').attr('rel', 'Cry').attr('title', 'Cry');
            $('#cco' + New_ID).text(function(i, val) {
              return val * 1 - 1
            });

            if ($('#cco' + New_ID).text() == 0) {
              $('#cry_count' + New_ID).remove();
              $("#ecry" + New_ID).hide('slow');
              $("#cry_count" + New_ID).hide('slow').prepend("<span id='cry_count" + New_ID + "' class='numcount bbc'><div class='icon-newL icon-crying-new lpos'></div><div class='cco' id='cco" + New_ID + "'>" + count + "</div></span>");
            } else if ($('#cco' + New_ID).text() <= 0) {
              $("#ecry" + New_ID).hide('slow');
            }
          }
        } else if (REL == 'UnAngry') {
          $('#ulk' + New_ID).removeClass('icon-angry-new').addClass('icon-like-blf');
          $('#eco' + New_ID).text(function(i, val) {
            return val * 1 - 1
          });
          if ($('#eco' + New_ID).text() == 0) {
            $("#angrys" + New_ID).hide('slow');
            $('#angry_count' + New_ID).remove();
          }
          if ($('#eloves' + New_ID).css('display') == 'none' && $('#ehaha' + New_ID).css('display') == 'none' && $('#ehihi' + New_ID).css('display') == 'none' && $('#elikes' + New_ID).css('display') == 'none' && $('#ewoww' + New_ID).css('display') == 'none' && $('#ecry' + New_ID).css('display') == 'none') {
            if ($('#eco' + New_ID).text() < 0) {
              $("#likess" + New_ID).hide('slow');
            }
          }

          $('#' + ID).html('<div class="icon-newL icon-angry-new"></div>').attr('rel', 'Angry').attr('title', 'Angry');
        }
        //Angry Finished
      }

    }
  });

  return false;
});
});