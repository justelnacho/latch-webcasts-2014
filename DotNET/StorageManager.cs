using System;
using System.Collections.Generic;
using System.Configuration;
using System.IO;
using System.Linq;
using System.Web;

namespace LatchTalks
{
    public static class StorageManager
    {

        private static string storageFile = Path.Combine(System.Web.Hosting.HostingEnvironment.ApplicationPhysicalPath, ConfigurationManager.AppSettings["latchStorageFile"]);

        public static string ReadAccount(string username)
        {
            if (File.Exists(storageFile))
            {
                using (LatchAccountsStorage.AccountsDataTable storage = new LatchAccountsStorage.AccountsDataTable())
                {
                    storage.ReadXml(storageFile);
                    var row = storage.Where(p => p.Username.Equals(username, StringComparison.InvariantCultureIgnoreCase)).FirstOrDefault();

                    if (row != null)
                        return row.AccountId;
                }
            }
            return string.Empty;
        }

        public static bool SaveAccount(string username, string account)
        {
            using (LatchAccountsStorage.AccountsDataTable storage = new LatchAccountsStorage.AccountsDataTable())
            {
                if (File.Exists(storageFile))
                    storage.ReadXml(storageFile);
                if (!storage.Any(p => p.Username.Equals(username, StringComparison.InvariantCultureIgnoreCase)))
                {
                    storage.AddAccountsRow(username, account);
                    storage.WriteXml(storageFile);
                    return true;
                }

                return false;
            }
        }

        public static bool RemoveAccount(string username)
        {
            using (LatchAccountsStorage.AccountsDataTable storage = new LatchAccountsStorage.AccountsDataTable())
            {
                if (File.Exists(storageFile))
                {
                    storage.ReadXml(storageFile);
                    var accountRow = storage.Where(p => p.Username.Equals(username, StringComparison.CurrentCultureIgnoreCase)).FirstOrDefault();
                    if (accountRow != null)
                    {
                        storage.RemoveAccountsRow(accountRow);
                        storage.WriteXml(storageFile);
                        return true;
                    }
                }
                return false;
            }
        }
    }
}