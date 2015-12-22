{*
* 2015 Darwin Pricing
*
* For support please visit www.darwinpricing.com
*
* NOTICE OF LICENSE
*
* This source file is subject to the GNU Lesser General Public License (LGPL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://www.gnu.org/licenses/lgpl.txt
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@darwinpricing.com so we can send you a copy immediately.
*
*  @author    Darwin Pricing <support@darwinpricing.com>
*  @copyright 2015 Darwin Pricing
*  @license   http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License (LGPL 3.0)
*}

{if isset($message.success)}
<div class="alert alert-success" role="alert">{$message.success|escape:'htmlall':'UTF-8'}</div>
{/if}
{if isset($message.warning)}
<div class="alert alert-warning" role="alert">{$message.warning|escape:'htmlall':'UTF-8'}</div>
{/if}
{if isset($message.danger)}
<div class="alert alert-danger" role="alert">{$message.danger|escape:'htmlall':'UTF-8'}</div>
{/if}

<div class="panel">
	<h3><i class="icon icon-globe"></i> {l s='Darwin Pricing - Geo-Targeted Sales for Profit' mod='darwinpricing'}</h3>
	<p>
		<strong>{l s='Welcome to Darwin Pricing!' mod='darwinpricing'}</strong><br />
	</p>
	<br />
	<p>
                <a href="https://www.darwinpricing.com/" target="_blank">Darwin Pricing</a> {l s='is the world\'s leading dynamic pricing solution for geo-targeted eCommerce.' mod='darwinpricing'}
		{l s='This module will boost your eCommerce profits with an Exit Intent coupon box, geo-targeted automatically at cities where local retailers beat you on price.' mod='darwinpricing'}
	</p>
	<br />
	<p>
                {l s='Please' mod='darwinpricing'} <a href="https://admin.darwinpricing.com/sign-up" target="_blank">{l s='create a free account' mod='darwinpricing'}</a> {l s='and' mod='darwinpricing'} <a href="https://admin.darwinpricing.com/sign-in" target="_blank">{l s='login' mod='darwinpricing'}</a> {l s='to configure your geo-targeted coupon box.' mod='darwinpricing'}
		{l s='Then enter your credentials in the form below to activate the module!' mod='darwinpricing'}
	</p>
</div>
