/*
 Easy Pie Coming Soon Plugin
 Copyright (C) 2013, Synthetic Thought LLC
 website: easypiewp.com contact: bob@easypiewp.com
 
 Easy Pie Coming Soon Plugin is distributed under the GNU General Public License, Version 3,
 June 2007. Copyright (C) 2007 Free Software Foundation, Inc., 51 Franklin
 St, Fifth Floor, Boston, MA 02110, USA
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

easyPie = {};
easyPie.CS = {};


jQuery(document).ready(function($) {

    // TODO: Init stuff

})


easyPie.CS.PurgeContact = function(confirmText, yesText, noText, contact_id, wpnonce, successCallback) {

    //var c = confirm(confirmText);

    jQuery("#easy-pie-cs-delete-confirm-text").text(confirmText);

    var dialogButtons = {};

    dialogButtons[yesText] = function() {
        var that = this;
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            dataType: "json",
            timeout: 10000000,
            data:
                    {
                        'action': 'EZP_CS_purge_contact',
                        'contact_id': contact_id,
                        '_wpnonce' : wpnonce
                    },
            beforeSend: function() {
                //   alert('beforesend');
            },
            complete: function() {
                //    alert('complete');
            },
            success: function(data) {
                jQuery(that).dialog("close");
                //  alert('succeess');
                successCallback();
                //            location.reload();
            },
            error: function(data) {
                jQuery(that).dialog("close");
                //  alert('error');                        
                //  location.reload();
            }
        });
    };
    dialogButtons[noText] = function() {
        jQuery(this).dialog("close");
    };


    jQuery("#easy-pie-cs-delete-confirm").dialog({
        resizable: false,
        //height: 40,
        modal: true,
        width: 350,
        buttons: dialogButtons
    });
}