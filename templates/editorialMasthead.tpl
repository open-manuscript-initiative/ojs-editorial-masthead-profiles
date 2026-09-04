{**
 * templates/editorialMasthead.tpl
 *
 * Plugin-owned card presentation for the native OJS 3.5 Editorial Masthead.
 * Uses the variables assigned by AboutContextHandler and does not require an
 * application or theme template to be changed on disk.
 *}
{include file="frontend/components/header.tpl" pageTitle="common.editorialMasthead"}

<div class="page page_masthead page_editorial_masthead_cards">
	{include file="frontend/components/breadcrumbs.tpl" currentTitleKey="common.editorialMasthead"}

	<h1>{translate key="common.editorialMasthead"}</h1>

	{foreach from=$mastheadRoles item="mastheadRole"}
		{if array_key_exists($mastheadRole->id, $mastheadUsers)}
			<section class="emc-role-section" aria-labelledby="emc-role-{$mastheadRole->id|escape}">
				<h2 class="emc-role-title" id="emc-role-{$mastheadRole->id|escape}">{$mastheadRole->getLocalizedData('name')|escape}</h2>
				<ul class="user_listing emc-grid" role="list">
				{foreach from=$mastheadUsers[$mastheadRole->id] item="mastheadUser"}
					{assign var=user value=$mastheadUser['user']}
					{assign var=userId value=$user->getId()}
					{assign var=affiliation value=$user->getLocalizedData('affiliation')}
					{assign var=userUrl value=$user->getData('url')}
					<li class="emc-card">
						{if !empty($editorialMastheadProfileImageUrls[$userId])}
							<img class="emc-photo" src="{$editorialMastheadProfileImageUrls[$userId]|escape}" alt="{$user->getFullName()|escape}" width="88" height="88" loading="lazy" decoding="async">
						{else}
							<div class="emc-photo-fallback" aria-hidden="true">{$user->getDisplayInitials()|escape}</div>
						{/if}
						<div class="emc-content">
							<h3 class="emc-name">
								<a class="editorial-masthead-profile-link emc-name-link" href="{url page="editorProfile" op="view" path=$userId}">{$user->getFullName()|escape}</a>
								{if $user->getData('orcid') && $user->hasVerifiedOrcid()}
									<span class="emc-orcid">
										<a href="{$user->getData('orcid')|escape}" target="_blank" rel="noopener noreferrer" aria-label="{translate key="common.editorialHistory.page.orcidLink" name=$user->getFullName()|escape}">
											{$orcidIcon}
										</a>
									</span>
								{/if}
							</h3>
							{if !empty($mastheadUser['dateStart'])}
								<span class="date_start emc-date">{translate key="common.fromUntil" from=$mastheadUser['dateStart'] until=""}</span>
							{/if}
							{if !empty($affiliation)}
								<span class="affiliation emc-affiliation">{$affiliation|escape}</span>
							{/if}
							{if !empty($userUrl)}
								<span class="emc-url"><a href="{$userUrl|escape}" target="_blank" rel="noopener noreferrer">{translate key="plugins.generic.editorialMastheadProfiles.website"}</a></span>
							{/if}
						</div>
					</li>
				{/foreach}
				</ul>
			</section>
		{/if}
	{/foreach}

	<p>
		{capture assign=editorialHistoryUrl}{url page="about" op="editorialHistory" router=\PKP\core\PKPApplication::ROUTE_PAGE}{/capture}
		{translate key="about.editorialMasthead.linkToEditorialHistory" url=$editorialHistoryUrl}
	</p>

	{if $reviewers->count()}
		<hr>
		<h2>{translate key="common.editorialMasthead.peerReviewers"}</h2>
		<p>{translate key="common.editorialMasthead.peerReviewers.description" year=$previousYear}</p>
		<ul class="user_listing" role="list">
		{foreach from=$reviewers item="reviewer"}
			<li>
				{strip}
					<span class="name">
						{$reviewer->getFullName()|escape}
						{if $reviewer->getData('orcid') && $reviewer->getData('orcidAccessToken')}
							<span class="orcid">
								<a href="{$reviewer->getData('orcid')|escape}" target="_blank" rel="noopener noreferrer" aria-label="{translate key="common.editorialHistory.page.orcidLink" name=$reviewer->getFullName()|escape}">
									{$orcidIcon}
								</a>
							</span>
						{/if}
					</span>
					{if !empty($reviewer->getLocalizedData('affiliation'))}
						<span class="affiliation">{$reviewer->getLocalizedData('affiliation')|escape}</span>
					{/if}
				{/strip}
			</li>
		{/foreach}
		</ul>
	{/if}
</div>

{include file="frontend/components/footer.tpl"}
