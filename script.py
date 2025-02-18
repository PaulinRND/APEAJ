import os
import shutil

openssl_bin = "C:/xampp/apache/bin"

os.environ['OPENSSL_CONF'] = 'C:/xampp/apache/conf/openssl.cnf'

if not os.path.exists('C:/xampp/apache/conf/ssl.crt'):
    os.makedirs('C:/xampp/apache/conf/ssl.crt')
if not os.path.exists('C:/xampp/apache/conf/ssl.key'):
    os.makedirs('C:/xampp/apache/conf/ssl.key')

os.system(os.path.join(openssl_bin, "openssl req -new -out server.csr"))
os.system(os.path.join(openssl_bin, "openssl rsa -in privkey.pem -out server.key"))
os.system(os.path.join(openssl_bin, "openssl x509 -in server.csr -out server.crt -req -signkey server.key -days 365"))

# os.environ['OPENSSL_CONF'] = ''
# os.remove('.rnd')
# os.remove('privkey.pem')
# os.remove('server.csr')

shutil.move('server.crt', 'C:/xampp/apache/conf/ssl.crt/server.crt')
shutil.move('server.key', 'C:/xampp/apache/conf/ssl.key/server.key')

print("End openssl")

config_file = "C:/xampp/apache/conf/extra/httpd-vhosts.conf"
new_config = """
<VirtualHost *:443>
ServerAdmin arthur.bourgoin@etu.iut-tlse3.fr
ServerName www.apeaj.local
DocumentRoot "C:/xampp/htdocs/APEAJ/public"
SSLEngine on
SSLCertificateFile "conf/ssl.crt/server.crt" 
SSLCertificateKeyFile "conf/ssl.key/server.key"
ErrorLog "logs/apeaj-error.log"
CustomLog "logs/apeaj-access.log" common
</VirtualHost>
"""
try:
    with open(config_file, "a") as file:
        file.write(new_config)
    print("Success vhost")
except Exception as e:
    print(f"Error vhost : {e}")

# try:
#     subprocess.run(["net", "stop", "Apache2.4.41"], check=True)
#     subprocess.run(["net", "start", "Apache2.4.41"], check=True)
#     print("Success apache")
# except subprocess.CalledProcessError as e:
#     print(f"Error apache : {e}")