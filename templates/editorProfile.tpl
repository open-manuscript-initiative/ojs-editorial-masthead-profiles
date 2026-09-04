{include file="frontend/components/header.tpl" pageTitleTranslated=$profileName}
<div class="page emp-profile">
    {include file="frontend/components/breadcrumbs.tpl" currentTitle=$profileName}
    <div class="emp-back-wrap"><a class="emp-back" href="{$editorialMastheadUrl|escape}">← {translate key="plugins.generic.editorialMastheadProfiles.backToMasthead"}</a></div>
    <article class="emp-card">
        {if $profileImageUrl}
            <img class="emp-photo" src="{$profileImageUrl|escape}" alt="{$profileName|escape}" width="150" height="150" loading="lazy" decoding="async">
        {else}
            <div class="emp-photo-fallback" aria-hidden="true">{$profileInitials|escape}</div>
        {/if}
        <div class="emp-main">
            <h1 class="emp-title">{$profileName|escape}</h1>
            {if $profileRoles|@count}
                <div class="emp-roles">
                    {foreach from=$profileRoles item=role}<span class="emp-role">{$role|escape}</span>{/foreach}
                </div>
            {/if}
            {if $profileAffiliation}
                <dl class="emp-details">
                    <div class="emp-detail">
                        <dt>{translate key="user.affiliation"}</dt>
                        <dd>{$profileAffiliation|escape}</dd>
                    </div>
                </dl>
            {/if}
            {if $profileOrcid || $profileUrl}
                <div class="emp-links">
                    {if $profileOrcid}<a href="{$profileOrcid|escape}" target="_blank" rel="noopener noreferrer">ORCID</a>{/if}
                    {if $profileUrl}<a href="{$profileUrl|escape}" target="_blank" rel="noopener noreferrer">{translate key="plugins.generic.editorialMastheadProfiles.website"}</a>{/if}
                </div>
            {/if}
        </div>
    </article>
    {if $profileBiography}
        <section class="emp-bio">
            <h2>{translate key="plugins.generic.editorialMastheadProfiles.biography"}</h2>
            <div class="emp-bio-content">{$profileBiography|strip_unsafe_html}</div>
        </section>
    {/if}
</div>
{include file="frontend/components/footer.tpl"}
