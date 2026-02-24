<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
	exclude-result-prefixes="sitemap">
	<xsl:output method="html" encoding="UTF-8" indent="yes" />
	<xsl:variable name="has-lastmod"    select="count( /sitemap:urlset/sitemap:url/sitemap:lastmod )"    />
	<xsl:variable name="has-changefreq" select="count( /sitemap:urlset/sitemap:url/sitemap:changefreq )" />
	<xsl:variable name="has-priority"   select="count( /sitemap:urlset/sitemap:url/sitemap:priority )"   />
	<xsl:template match="/">
		<html lang="ko">
			<head>
				<meta charset="UTF-8" />
				<title>법무법인 동주 | 수원 성범죄 sitemap</title>
				<style type="text/css">
					body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; margin: 1rem 2rem; color: #1a1a1a; }
					#sitemap__header { margin-bottom: 1.5rem; }
					#sitemap__header h1 { font-size: 1.5rem; font-weight: 600; margin: 0 0 0.5rem; }
					#sitemap__table { border-collapse: collapse; width: 100%; max-width: 900px; }
					#sitemap__table th, #sitemap__table td { padding: 0.5rem 0.75rem; text-align: left; border-bottom: 1px solid #e0e0e0; }
					#sitemap__table th { font-weight: 600; background: #f5f5f5; }
					#sitemap__table tr:nth-child(even) td { background: #fafafa; }
					#sitemap__table a { color: #1967d2; text-decoration: none; }
					#sitemap__table a:hover { text-decoration: underline; }
				</style>
			</head>
			<body>
				<div id="sitemap">
					<div id="sitemap__header">
						<h1>법무법인 동주 | 수원 성범죄 sitemap</h1>
					</div>
					<div id="sitemap__content">
						<table id="sitemap__table">
							<thead>
								<tr>
									<th class="loc">URL</th>
									<xsl:if test="$has-lastmod">
										<th class="lastmod">Last Modified</th>
									</xsl:if>
									<xsl:if test="$has-changefreq">
										<th class="changefreq">Change Frequency</th>
									</xsl:if>
									<xsl:if test="$has-priority">
										<th class="priority">Priority</th>
									</xsl:if>
								</tr>
							</thead>
							<tbody>
								<xsl:for-each select="sitemap:urlset/sitemap:url">
									<tr>
										<td class="loc"><a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc" /></a></td>
										<xsl:if test="$has-lastmod">
											<td class="lastmod"><xsl:value-of select="sitemap:lastmod" /></td>
										</xsl:if>
										<xsl:if test="$has-changefreq">
											<td class="changefreq"><xsl:value-of select="sitemap:changefreq" /></td>
										</xsl:if>
										<xsl:if test="$has-priority">
											<td class="priority"><xsl:value-of select="sitemap:priority" /></td>
										</xsl:if>
									</tr>
								</xsl:for-each>
							</tbody>
						</table>
					</div>
				</div>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>
