using LatchSDK;
using LatchTalks.Filters;
using LatchTalks.Models;
using System;
using System.Collections.Generic;
using System.Configuration;
using System.Web.Mvc;
using System.Web.Security;
using WebMatrix.WebData;

namespace LatchTalks.Controllers
{
    [Authorize]
    [InitializeSimpleMembership]
    public class AccountController : Controller
    {
        //
        // GET: /Account/Login

        [AllowAnonymous]
        public ActionResult Login(string returnUrl)
        {
            ViewBag.ReturnUrl = returnUrl;
            return View();
        }

        //
        // POST: /Account/Login

        [HttpPost]
        [AllowAnonymous]
        [ValidateAntiForgeryToken]
        public ActionResult Login(LoginModel model, string returnUrl)
        {

            if (ModelState.IsValid && Membership.ValidateUser(model.UserName, model.Password))//WebSecurity.Login(model.UserName, model.Password, persistCookie: model.RememberMe))
            {
                if (IsLatchOperationOpen(model.UserName, ConfigurationManager.AppSettings["latchAppId"]))
                {
                    WebSecurity.Login(model.UserName, model.Password, persistCookie: model.RememberMe);
                    //return RedirectToLocal(returnUrl);
                    return RedirectToAction("Manage", "Account");
                }
            }

            // Si llegamos a este punto, es que se ha producido un error y volvemos a mostrar el formulario
            ModelState.AddModelError("", "El nombre de usuario o la contraseña especificados son incorrectos.");
            return View(model);
        }

        //
        // POST: /Account/LogOff

        [HttpPost]
        [ValidateAntiForgeryToken]
        public ActionResult LogOff()
        {
            WebSecurity.Logout();

            return RedirectToAction("Index", "Home");
        }

        //
        // GET: /Account/Register

        [AllowAnonymous]
        public ActionResult Register()
        {
            return View();
        }

        //
        // POST: /Account/Register

        [HttpPost]
        [AllowAnonymous]
        [ValidateAntiForgeryToken]
        public ActionResult Register(RegisterModel model)
        {
            if (ModelState.IsValid)
            {
                // Intento de registrar al usuario
                try
                {
                    WebSecurity.CreateUserAndAccount(model.UserName, model.Password);
                    WebSecurity.Login(model.UserName, model.Password);
                    return RedirectToAction("Index", "Home");
                }
                catch (MembershipCreateUserException e)
                {
                    ModelState.AddModelError("", ErrorCodeToString(e.StatusCode));
                }
            }

            // Si llegamos a este punto, es que se ha producido un error y volvemos a mostrar el formulario
            return View(model);
        }


        //
        // GET: /Account/Manage

        public ActionResult Manage(ManageMessageId? message)
        {
            ViewBag.StatusMessage =
                message == ManageMessageId.ChangePasswordSuccess ? "La contraseña se ha cambiado."
                : message == ManageMessageId.SetPasswordSuccess ? "Su contraseña se ha establecido."
                : message == ManageMessageId.RemoveLoginSuccess ? "El inicio de sesión externo se ha quitado."
                : message == ManageMessageId.LatchPairSuccess ? "Cuenta pareada con Latch correctamente."
                 : message == ManageMessageId.LatchUnpairSuccess ? "Te has despareado correctamente."
                : "";
            ViewBag.HasLocalPassword = true;
            ViewBag.ReturnUrl = Url.Action("Manage");

            ViewBag.LatchAccountId = StorageManager.ReadAccount(User.Identity.Name);

            return View();
        }

        //
        // POST: /Account/Manage

        [HttpPost]
        [ValidateAntiForgeryToken]
        public ActionResult Manage(LocalPasswordModel model)
        {
            ViewBag.HasLocalPassword = true;
            ViewBag.ReturnUrl = Url.Action("Manage");
            if (ModelState.IsValid)
            {
                // ChangePassword iniciará una excepción en lugar de devolver false en determinados escenarios de error.
                bool changePasswordSucceeded;
                try
                {
                    changePasswordSucceeded = IsLatchOperationOpen(User.Identity.Name, ConfigurationManager.AppSettings["editPasswordOperationid"]) && WebSecurity.ChangePassword(User.Identity.Name, model.OldPassword, model.NewPassword);
                }
                catch (Exception)
                {
                    changePasswordSucceeded = false;
                }

                if (changePasswordSucceeded)
                {
                    return RedirectToAction("Manage", new { Message = ManageMessageId.ChangePasswordSuccess });
                }
                else
                {
                    ModelState.AddModelError("", "La contraseña actual es incorrecta o la nueva contraseña no es válida.");
                }
            }

            // Si llegamos a este punto, es que se ha producido un error y volvemos a mostrar el formulario
            return View(model);
        }

        [HttpPost]
        [ValidateAntiForgeryToken]
        public ActionResult Pair(string token)
        {
            LatchSDK.Latch latchComm = new LatchSDK.Latch(ConfigurationManager.AppSettings["latchAppId"], ConfigurationManager.AppSettings["latchAppSecret"]);
            LatchResponse response = latchComm.Pair(token);
            if (response.Error == null && response.Data.ContainsKey("accountId"))
            {
                StorageManager.SaveAccount(User.Identity.Name, response.Data["accountId"] as string);
                return RedirectToAction("Manage", new { Message = ManageMessageId.LatchPairSuccess });
            }
            else
            {
                ModelState.AddModelError("", response.Error.Message);
                return View("Manage");
            }
        }

        [HttpPost]
        [ValidateAntiForgeryToken]
        public ActionResult Unpair()
        {
            LatchSDK.Latch latchComm = new LatchSDK.Latch(ConfigurationManager.AppSettings["latchAppId"], ConfigurationManager.AppSettings["latchAppSecret"]);
            LatchResponse response = latchComm.Unpair(StorageManager.ReadAccount(User.Identity.Name));
            if (response.Error == null)
            {
                StorageManager.RemoveAccount(User.Identity.Name);
                return RedirectToAction("Manage", new { Message = ManageMessageId.LatchUnpairSuccess });
            }
            else
            {
                ModelState.AddModelError("", response.Error.Message);
                return View("Manage");
            }
        }

        #region Aplicaciones auxiliares
        private ActionResult RedirectToLocal(string returnUrl)
        {
            if (Url.IsLocalUrl(returnUrl))
            {
                return Redirect(returnUrl);
            }
            else
            {
                return RedirectToAction("Index", "Home");
            }
        }

        public enum ManageMessageId
        {
            ChangePasswordSuccess,
            SetPasswordSuccess,
            RemoveLoginSuccess,
            LatchPairSuccess,
            LatchUnpairSuccess
        }

        private static string ErrorCodeToString(MembershipCreateStatus createStatus)
        {
            // Vaya a http://go.microsoft.com/fwlink/?LinkID=177550 para
            // obtener una lista completa de códigos de estado.
            switch (createStatus)
            {
                case MembershipCreateStatus.DuplicateUserName:
                    return "El nombre de usuario ya existe. Escriba un nombre de usuario diferente.";

                case MembershipCreateStatus.DuplicateEmail:
                    return "Ya existe un nombre de usuario para esa dirección de correo electrónico. Escriba una dirección de correo electrónico diferente.";

                case MembershipCreateStatus.InvalidPassword:
                    return "La contraseña especificada no es válida. Escriba un valor de contraseña válido.";

                case MembershipCreateStatus.InvalidEmail:
                    return "La dirección de correo electrónico especificada no es válida. Compruebe el valor e inténtelo de nuevo.";

                case MembershipCreateStatus.InvalidAnswer:
                    return "La respuesta de recuperación de la contraseña especificada no es válida. Compruebe el valor e inténtelo de nuevo.";

                case MembershipCreateStatus.InvalidQuestion:
                    return "La pregunta de recuperación de la contraseña especificada no es válida. Compruebe el valor e inténtelo de nuevo.";

                case MembershipCreateStatus.InvalidUserName:
                    return "El nombre de usuario especificado no es válido. Compruebe el valor e inténtelo de nuevo.";

                case MembershipCreateStatus.ProviderError:
                    return "El proveedor de autenticación devolvió un error. Compruebe los datos especificados e inténtelo de nuevo. Si el problema continúa, póngase en contacto con el administrador del sistema.";

                case MembershipCreateStatus.UserRejected:
                    return "La solicitud de creación de usuario se ha cancelado. Compruebe los datos especificados e inténtelo de nuevo. Si el problema continúa, póngase en contacto con el administrador del sistema.";

                default:
                    return "Error desconocido. Compruebe los datos especificados e inténtelo de nuevo. Si el problema continúa, póngase en contacto con el administrador del sistema.";
            }
        }


        private bool IsLatchOperationOpen(string username, string operationId)
        {
            bool isOpen = true;
            try
            {
                string accountId = StorageManager.ReadAccount(username);
                if (!string.IsNullOrEmpty(accountId))
                {
                    LatchSDK.Latch latchComm = new LatchSDK.Latch(ConfigurationManager.AppSettings["latchAppId"], ConfigurationManager.AppSettings["latchAppSecret"]);
                    LatchResponse response = latchComm.OperationStatus(accountId, operationId);
                    //isOpen = response.Error == null && ((string)(((Dictionary<string, object>)((Dictionary<string, object>)response.Data["operations"])[operationId])["status"])).Equals("on", StringComparison.InvariantCultureIgnoreCase);

                    if (response.Error == null && response.Data["operations"] is Dictionary<string, object>)
                    {
                        Dictionary<string, object> operations = response.Data["operations"] as Dictionary<string, object>;
                        if (operations[operationId] is Dictionary<string, object>)
                        {
                            Dictionary<string, object> currentOperation = operations[operationId] as Dictionary<string, object>;
                            isOpen = (currentOperation["status"] as string).Equals("on", StringComparison.InvariantCultureIgnoreCase);
                        }
                    }

                }

            }
            catch (Exception)
            {
            }
            return isOpen;
        }

        #endregion
    }
}
